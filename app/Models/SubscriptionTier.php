<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionTier extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'price',
        'billing_period',
        'max_users',
        'max_storage_mb',
        'analytics_basic',
        'analytics_advanced',
        'analytics_export',
        'custom_branding',
        'remove_branding',
        'custom_domain',
        'webhooks_enabled',
        'api_access',
        'sso_enabled',
        'advanced_moderation',
        'support_level',
        'support_response_hours',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_users' => 'integer',
        'max_storage_mb' => 'integer',
        'analytics_basic' => 'boolean',
        'analytics_advanced' => 'boolean',
        'analytics_export' => 'boolean',
        'custom_branding' => 'boolean',
        'remove_branding' => 'boolean',
        'custom_domain' => 'boolean',
        'webhooks_enabled' => 'boolean',
        'api_access' => 'boolean',
        'sso_enabled' => 'boolean',
        'advanced_moderation' => 'boolean',
        'is_active' => 'boolean',
        'support_response_hours' => 'integer',
        'sort_order' => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function hasUnlimitedUsers(): bool
    {
        return $this->max_users === null;
    }

    public function hasUnlimitedStorage(): bool
    {
        return $this->max_storage_mb === null;
    }

    public function getStorageDisplayAttribute(): string
    {
        if ($this->max_storage_mb === null) {
            return 'Unlimited';
        }

        if ($this->max_storage_mb === 0) {
            return 'None';
        }

        $gb = $this->max_storage_mb / 1024;
        return $gb >= 1 ? round($gb, 1) . ' GB' : $this->max_storage_mb . ' MB';
    }

    public function getUserLimitDisplayAttribute(): string
    {
        return $this->max_users === null ? 'Unlimited' : number_format($this->max_users);
    }

    public function getPriceDisplayAttribute(): string
    {
        return $this->price == 0 ? 'Free' : '$' . number_format($this->price, 2) . '/' . $this->billing_period;
    }
}
