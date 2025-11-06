<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'subscription_tier_id',
        'status',
        'started_at',
        'trial_ends_at',
        'expires_at',
        'cancelled_at',
        'current_users',
        'current_storage_mb',
        'stripe_subscription_id',
        'stripe_customer_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'current_users' => 'integer',
        'current_storage_mb' => 'integer',
    ];

    public function tier(): BelongsTo
    {
        return $this->belongsTo(SubscriptionTier::class, 'subscription_tier_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function hasFeature(string $feature): bool
    {
        return $this->tier->{$feature} ?? false;
    }

    public function canAddUser(): bool
    {
        if (!$this->tier->max_users) {
            return true; // Unlimited users
        }

        return $this->current_users < $this->tier->max_users;
    }

    public function canUploadFile(int $fileSizeMb): bool
    {
        if (!$this->tier->max_storage_mb) {
            return true; // Unlimited storage
        }

        return ($this->current_storage_mb + $fileSizeMb) <= $this->tier->max_storage_mb;
    }

    public function getStorageUsedPercentageAttribute(): float
    {
        if (!$this->tier->max_storage_mb) {
            return 0;
        }

        return ($this->current_storage_mb / $this->tier->max_storage_mb) * 100;
    }

    public function getUsersUsedPercentageAttribute(): float
    {
        if (!$this->tier->max_users) {
            return 0;
        }

        return ($this->current_users / $this->tier->max_users) * 100;
    }

    public function updateUserCount(): void
    {
        $this->current_users = User::count();
        $this->save();
    }

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
