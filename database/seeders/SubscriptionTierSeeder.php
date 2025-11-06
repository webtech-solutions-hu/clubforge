<?php

namespace Database\Seeders;

use App\Models\SubscriptionTier;
use Illuminate\Database\Seeder;

class SubscriptionTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'starter',
                'display_name' => 'Starter',
                'description' => 'Perfect for small clubs just getting started',
                'price' => 0.00,
                'billing_period' => 'monthly',
                'max_users' => 200,
                'max_storage_mb' => null, // No storage/very limited
                'analytics_basic' => true,
                'analytics_advanced' => false,
                'analytics_export' => false,
                'custom_branding' => false,
                'remove_branding' => false,
                'custom_domain' => false,
                'webhooks_enabled' => false,
                'api_access' => false,
                'sso_enabled' => false,
                'advanced_moderation' => false,
                'support_level' => 'email',
                'support_response_hours' => 72,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'pro',
                'display_name' => 'Pro',
                'description' => 'For growing clubs with advanced needs',
                'price' => 29.00,
                'billing_period' => 'monthly',
                'max_users' => 1000,
                'max_storage_mb' => 5120, // 5 GB
                'analytics_basic' => true,
                'analytics_advanced' => true,
                'analytics_export' => false,
                'custom_branding' => true,
                'remove_branding' => false,
                'custom_domain' => false,
                'webhooks_enabled' => false,
                'api_access' => false,
                'sso_enabled' => false,
                'advanced_moderation' => false,
                'support_level' => 'priority',
                'support_response_hours' => 48,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'club_plus',
                'display_name' => 'Club+',
                'description' => 'Unlimited users with powerful integrations',
                'price' => 79.00,
                'billing_period' => 'monthly',
                'max_users' => null, // Unlimited
                'max_storage_mb' => 51200, // 50 GB
                'analytics_basic' => true,
                'analytics_advanced' => true,
                'analytics_export' => true,
                'custom_branding' => true,
                'remove_branding' => true,
                'custom_domain' => false,
                'webhooks_enabled' => true,
                'api_access' => true,
                'sso_enabled' => false,
                'advanced_moderation' => true,
                'support_level' => 'priority',
                'support_response_hours' => 24,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'enterprise',
                'display_name' => 'Enterprise',
                'description' => 'Complete white-label solution with dedicated support',
                'price' => 199.00,
                'billing_period' => 'monthly',
                'max_users' => null, // Unlimited
                'max_storage_mb' => null, // Custom/Dedicated
                'analytics_basic' => true,
                'analytics_advanced' => true,
                'analytics_export' => true,
                'custom_branding' => true,
                'remove_branding' => true,
                'custom_domain' => true,
                'webhooks_enabled' => true,
                'api_access' => true,
                'sso_enabled' => true,
                'advanced_moderation' => true,
                'support_level' => 'dedicated',
                'support_response_hours' => 4,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($tiers as $tier) {
            SubscriptionTier::updateOrCreate(
                ['name' => $tier['name']],
                $tier
            );
        }
    }
}
