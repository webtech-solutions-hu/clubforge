<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'sender_id',
        'recipient_type',
        'recipient_roles',
        'category',
        'title',
        'message',
        'priority',
        'icon',
        'icon_color',
        'action_url',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'recipient_roles' => 'array',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }

    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeForUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent(Builder $query, int $limit = 10): Builder
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }

    /**
     * Scope to get messages visible to a specific user based on recipient type and roles
     */
    public function scopeVisibleToUser(Builder $query, User $user): Builder
    {
        return $query->where(function ($q) use ($user) {
            // Global messages
            $q->where('recipient_type', 'global')
                // Direct user messages
                ->orWhere(function ($query) use ($user) {
                    $query->where('recipient_type', 'user')
                        ->where('user_id', $user->id);
                })
                // Role-based messages
                ->orWhere(function ($query) use ($user) {
                    $query->where('recipient_type', 'role')
                        ->where(function ($q) use ($user) {
                            $userRoleIds = $user->roles->pluck('id')->toArray();
                            foreach ($userRoleIds as $roleId) {
                                $q->orWhereJsonContains('recipient_roles', $roleId);
                            }
                        });
                });
        });
    }

    /**
     * Send a global message to all users
     */
    public static function sendGlobal(
        string $title,
        string $message,
        User $sender,
        ?string $category = 'general',
        ?string $priority = 'normal',
        ?string $icon = null,
        ?string $iconColor = 'gray',
        ?string $actionUrl = null,
        ?array $data = null
    ): self {
        return self::create([
            'sender_id' => $sender->id,
            'recipient_type' => 'global',
            'category' => $category,
            'title' => $title,
            'message' => $message,
            'priority' => $priority,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'action_url' => $actionUrl,
            'data' => $data,
        ]);
    }

    /**
     * Send a message to specific users
     */
    public static function sendToUser(
        User $recipient,
        string $title,
        string $message,
        User $sender,
        ?string $category = 'general',
        ?string $priority = 'normal',
        ?string $icon = null,
        ?string $iconColor = 'gray',
        ?string $actionUrl = null,
        ?array $data = null
    ): self {
        return self::create([
            'user_id' => $recipient->id,
            'sender_id' => $sender->id,
            'recipient_type' => 'user',
            'category' => $category,
            'title' => $title,
            'message' => $message,
            'priority' => $priority,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'action_url' => $actionUrl,
            'data' => $data,
        ]);
    }

    /**
     * Send a message to users with specific roles
     */
    public static function sendToRoles(
        array $roleIds,
        string $title,
        string $message,
        User $sender,
        ?string $category = 'general',
        ?string $priority = 'normal',
        ?string $icon = null,
        ?string $iconColor = 'gray',
        ?string $actionUrl = null,
        ?array $data = null
    ): self {
        return self::create([
            'sender_id' => $sender->id,
            'recipient_type' => 'role',
            'recipient_roles' => $roleIds,
            'category' => $category,
            'title' => $title,
            'message' => $message,
            'priority' => $priority,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'action_url' => $actionUrl,
            'data' => $data,
        ]);
    }
}
