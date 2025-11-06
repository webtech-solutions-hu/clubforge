<?php

namespace App\Filament\Admin\Widgets;

use App\Services\SubscriptionService;
use Filament\Widgets\Widget;

class SubscriptionOverview extends Widget
{
    protected static string $view = 'filament.widgets.subscription-overview';

    protected static ?int $sort = -1; // Show before other widgets

    protected int | string | array $columnSpan = 'full';

    /**
     * Check if widget can be displayed
     */
    public static function canView(): bool
    {
        $user = auth()->user();

        // Only show to Administrators and Owners
        return $user && ($user->hasRole(['Administrator', 'Owner']) || $user->isSupervisor());
    }

    /**
     * Get widget data
     */
    protected function getViewData(): array
    {
        $license = SubscriptionService::current();
        $tier = SubscriptionService::getCurrentTier();
        $tierName = SubscriptionService::getCurrentTierName();

        $data = [
            'tier_name' => $tierName,
            'tier_slug' => $license?->tier ?? 'starter',
            'license_key' => $license?->license_key ?? null,
            'is_active' => $license?->isActive() ?? false,
            'expires_at' => $license?->expires_at,
            'is_expired' => $license?->isExpired() ?? false,
        ];

        // User capacity
        $maxUsers = $tier['max_users'] ?? null;
        $currentUsers = $license?->current_users ?? \App\Models\User::count();
        $usersPercentage = $maxUsers ? ($currentUsers / $maxUsers) * 100 : 0;

        $data['users'] = [
            'current' => $currentUsers,
            'max' => $maxUsers,
            'remaining' => $maxUsers ? max(0, $maxUsers - $currentUsers) : null,
            'percentage' => $usersPercentage,
            'unlimited' => $maxUsers === null,
        ];

        // Storage capacity
        $maxStorage = $tier['max_storage_mb'] ?? null;
        $currentStorage = $license?->current_storage_mb ?? 0;
        $storagePercentage = $maxStorage ? ($currentStorage / $maxStorage) * 100 : 0;

        $data['storage'] = [
            'current_mb' => $currentStorage,
            'current_gb' => round($currentStorage / 1024, 2),
            'max_mb' => $maxStorage,
            'max_gb' => $maxStorage ? round($maxStorage / 1024, 1) : null,
            'remaining_gb' => $maxStorage ? round(($maxStorage - $currentStorage) / 1024, 2) : null,
            'percentage' => $storagePercentage,
            'unlimited' => $maxStorage === null,
        ];

        // Features
        $data['features'] = $tier['features'] ?? [];

        return $data;
    }
}
