<?php

namespace App\Services;

use App\Models\License;
use App\Models\User;

class SubscriptionService
{
    protected static ?License $currentLicense = null;

    /**
     * Get the current active license (singleton pattern for app-wide license)
     */
    public static function current(): ?License
    {
        if (static::$currentLicense === null) {
            static::$currentLicense = License::where('status', 'active')->first();
        }

        return static::$currentLicense;
    }

    /**
     * Get current tier configuration
     */
    public static function getCurrentTier(): array
    {
        $license = static::current();

        if (!$license) {
            return config('subscription.tiers.starter');
        }

        return $license->getTierConfig();
    }

    /**
     * Get current tier name
     */
    public static function getCurrentTierName(): string
    {
        $license = static::current();

        if (!$license) {
            return 'Starter';
        }

        return $license->tier_name;
    }

    /**
     * Check if a specific feature is available
     */
    public static function hasFeature(string $feature): bool
    {
        $tier = static::getCurrentTier();
        return $tier['features'][$feature] ?? false;
    }

    /**
     * Check if user limit has been reached
     */
    public static function canAddUser(): bool
    {
        $license = static::current();

        if (!$license) {
            // Default to starter tier limit
            $starterLimit = config('subscription.tiers.starter.max_users', 200);
            return User::count() < $starterLimit;
        }

        return $license->canAddUser();
    }

    /**
     * Check if storage limit allows file upload
     */
    public static function canUploadFile(int $fileSizeMb): bool
    {
        $license = static::current();

        if (!$license) {
            return false; // Starter tier has no storage
        }

        return $license->canUploadFile($fileSizeMb);
    }

    /**
     * Update usage stats
     */
    public static function updateUsage(): void
    {
        $license = static::current();

        if ($license) {
            $license->updateUserCount();
            $license->updateStorageUsage();
        }
    }

    /**
     * Check if branding should be shown
     */
    public static function showBranding(): bool
    {
        return !static::hasFeature('remove_branding');
    }

    /**
     * Get remaining user slots
     */
    public static function getRemainingUserSlots(): ?int
    {
        $tier = static::getCurrentTier();
        $maxUsers = $tier['max_users'] ?? null;

        if ($maxUsers === null) {
            return null; // Unlimited
        }

        $license = static::current();
        $currentUsers = $license?->current_users ?? User::count();

        return max(0, $maxUsers - $currentUsers);
    }

    /**
     * Get remaining storage in MB
     */
    public static function getRemainingStorage(): ?int
    {
        $tier = static::getCurrentTier();
        $maxStorage = $tier['max_storage_mb'] ?? null;

        if ($maxStorage === null) {
            return null; // Unlimited
        }

        $license = static::current();
        $currentStorage = $license?->current_storage_mb ?? 0;

        return max(0, $maxStorage - $currentStorage);
    }

    /**
     * Activate a license
     */
    public static function activateLicense(string $licenseKey, string $tier, ?\Carbon\Carbon $expiresAt = null): License
    {
        // Deactivate any existing licenses
        License::where('status', 'active')->update(['status' => 'inactive']);

        // Create and activate new license
        $license = License::create([
            'license_key' => $licenseKey,
            'tier' => $tier,
            'status' => 'active',
            'activated_at' => now(),
            'expires_at' => $expiresAt,
            'current_users' => User::count(),
            'current_storage_mb' => 0,
        ]);

        // Update storage usage
        $license->updateStorageUsage();

        // Clear cached license
        static::$currentLicense = null;

        return $license;
    }

    /**
     * Deactivate current license
     */
    public static function deactivateLicense(): void
    {
        $license = static::current();

        if ($license) {
            $license->update(['status' => 'inactive']);
            static::$currentLicense = null;
        }
    }
}
