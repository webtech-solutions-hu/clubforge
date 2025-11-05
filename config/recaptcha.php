<?php

return [
    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Enterprise Site Key
    |--------------------------------------------------------------------------
    |
    | The site key for your reCAPTCHA Enterprise implementation. This is used
    | in the frontend to execute invisible reCAPTCHA Enterprise.
    |
    */
    'site_key' => env('RECAPTCHA_SITE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Google Cloud Project ID
    |--------------------------------------------------------------------------
    |
    | The Google Cloud Project ID for reCAPTCHA Enterprise. Required for
    | backend validation and risk assessment.
    |
    */
    'project_id' => env('RECAPTCHA_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable reCAPTCHA validation globally. Useful for disabling
    | reCAPTCHA in development or testing environments.
    |
    */
    'enabled' => env('RECAPTCHA_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Minimum Risk Score
    |--------------------------------------------------------------------------
    |
    | The minimum acceptable risk score for reCAPTCHA Enterprise (0.0 to 1.0).
    | Higher scores indicate lower risk. Typical threshold is 0.5.
    |
    */
    'min_score' => env('RECAPTCHA_MIN_SCORE', 0.5),

    /*
    |--------------------------------------------------------------------------
    | Action Names
    |--------------------------------------------------------------------------
    |
    | Define custom action names for different forms. Used in Enterprise mode
    | to track and analyze different user interactions separately.
    |
    */
    'actions' => [
        'login' => 'LOGIN',
        'register' => 'REGISTER',
        'password_reset' => 'PASSWORD_RESET',
        'contact' => 'CONTACT',
    ],
];
