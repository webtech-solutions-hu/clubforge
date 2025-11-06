<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Tiers
    |--------------------------------------------------------------------------
    |
    | Define the available subscription tiers for ClubForge.
    | Each environment activates a tier using a license key.
    |
    */

    'tiers' => [
        'starter' => [
            'name' => 'Starter',
            'description' => 'Perfect for small clubs just getting started',
            'price' => 0.00,
            'max_users' => 200,
            'max_storage_mb' => null, // No storage
            'features' => [
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
            ],
            'support_hours' => 72,
        ],

        'pro' => [
            'name' => 'Pro',
            'description' => 'For growing clubs with advanced needs',
            'price' => 29.00,
            'max_users' => 1000,
            'max_storage_mb' => 5120, // 5 GB
            'features' => [
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
            ],
            'support_hours' => 48,
        ],

        'club_plus' => [
            'name' => 'Club+',
            'description' => 'Unlimited users with powerful integrations',
            'price' => 79.00,
            'max_users' => null, // Unlimited
            'max_storage_mb' => 51200, // 50 GB
            'features' => [
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
            ],
            'support_hours' => 24,
        ],

        'enterprise' => [
            'name' => 'Enterprise',
            'description' => 'Complete white-label solution with dedicated support',
            'price' => 199.00,
            'max_users' => null, // Unlimited
            'max_storage_mb' => null, // Unlimited
            'features' => [
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
            ],
            'support_hours' => 4,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Tier
    |--------------------------------------------------------------------------
    |
    | The default tier to use when no license is activated.
    |
    */

    'default_tier' => 'starter',

    /*
    |--------------------------------------------------------------------------
    | License Settings
    |--------------------------------------------------------------------------
    */

    'license' => [
        // License validation endpoint (for future use)
        'validation_endpoint' => env('LICENSE_VALIDATION_ENDPOINT'),

        // Grace period in days after license expiration
        'grace_period_days' => 7,
    ],
];
