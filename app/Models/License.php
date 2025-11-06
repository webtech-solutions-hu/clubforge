<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'license_key',
        'tier',
        'status',
        'activated_at',
        'expires_at',
        'current_users',
        'current_storage_mb',
        'metadata',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'current_users' => 'integer',
        'current_storage_mb' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the tier configuration
     */
    public function getTierConfig(): array
    {
        return config("subscription.tiers.{$this->tier}", config('subscription.tiers.starter'));
    }

    /**
     * Get tier display name
     */
    public function getTierNameAttribute(): string
    {
        return $this->getTierConfig()['name'] ?? 'Unknown';
    }

    /**
     * Get tier price
     */
    public function getPriceAttribute(): float
    {
        return $this->getTierConfig()['price'] ?? 0;
    }

    /**
     * Check if license is active
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if license is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if a feature is enabled
     */
    public function hasFeature(string $feature): bool
    {
        $config = $this->getTierConfig();
        return $config['features'][$feature] ?? false;
    }

    /**
     * Get max users for this tier
     */
    public function getMaxUsersAttribute(): ?int
    {
        return $this->getTierConfig()['max_users'];
    }

    /**
     * Get max storage for this tier
     */
    public function getMaxStorageMbAttribute(): ?int
    {
        return $this->getTierConfig()['max_storage_mb'];
    }

    /**
     * Check if can add more users
     */
    public function canAddUser(): bool
    {
        if ($this->max_users === null) {
            return true; // Unlimited
        }

        return $this->current_users < $this->max_users;
    }

    /**
     * Check if can upload file
     */
    public function canUploadFile(int $fileSizeMb): bool
    {
        if ($this->max_storage_mb === null) {
            return true; // Unlimited
        }

        return ($this->current_storage_mb + $fileSizeMb) <= $this->max_storage_mb;
    }

    /**
     * Get users used percentage
     */
    public function getUsersUsedPercentageAttribute(): float
    {
        if (!$this->max_users) {
            return 0;
        }

        return ($this->current_users / $this->max_users) * 100;
    }

    /**
     * Get storage used percentage
     */
    public function getStorageUsedPercentageAttribute(): float
    {
        if (!$this->max_storage_mb) {
            return 0;
        }

        return ($this->current_storage_mb / $this->max_storage_mb) * 100;
    }

    /**
     * Update user count
     */
    public function updateUserCount(): void
    {
        $this->current_users = User::count();
        $this->save();
    }

    /**
     * Update storage usage
     */
    public function updateStorageUsage(): void
    {
        // Calculate total storage from avatars, posts, and events
        $avatarsSize = \DB::table('users')
            ->whereNotNull('avatar')
            ->get()
            ->sum(function ($user) {
                $path = storage_path('app/public/' . $user->avatar);
                return file_exists($path) ? filesize($path) : 0;
            });

        $postsSize = \DB::table('posts')
            ->whereNotNull('image')
            ->get()
            ->sum(function ($post) {
                $path = storage_path('app/public/' . $post->image);
                return file_exists($path) ? filesize($path) : 0;
            });

        $eventsSize = \DB::table('events')
            ->whereNotNull('image')
            ->get()
            ->sum(function ($event) {
                $path = storage_path('app/public/' . $event->image);
                return file_exists($path) ? filesize($path) : 0;
            });

        $totalBytes = $avatarsSize + $postsSize + $eventsSize;
        $this->current_storage_mb = (int) ceil($totalBytes / 1024 / 1024);
        $this->save();
    }
}
