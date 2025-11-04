<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Version
    |--------------------------------------------------------------------------
    |
    | This configuration defines the current version of Club Forge.
    | The version system supports three stages: alpha, beta, and stable.
    |
    */

    // Major version number
    'major' => 1,

    // Minor version number
    'minor' => 0,

    // Patch version number
    'patch' => 0,

    // Version stage: 'alpha', 'beta', or 'stable'
    'stage' => 'alpha',

    // Stage version number (e.g., alpha.1, beta.2)
    'stage_version' => 1,

    /*
    |--------------------------------------------------------------------------
    | Version Display Format
    |--------------------------------------------------------------------------
    |
    | Customize how the version is displayed.
    |
    */

    // Full version string (e.g., "v1.0.0-alpha.1")
    'full' => function () {
        $major = config('version.major');
        $minor = config('version.minor');
        $patch = config('version.patch');
        $stage = config('version.stage');
        $stageVersion = config('version.stage_version');

        $version = "v{$major}.{$minor}.{$patch}";

        if ($stage !== 'stable') {
            $version .= "-{$stage}.{$stageVersion}";
        }

        return $version;
    },

    // Short version string (e.g., "1.0.0")
    'short' => function () {
        $major = config('version.major');
        $minor = config('version.minor');
        $patch = config('version.patch');

        return "{$major}.{$minor}.{$patch}";
    },

    /*
    |--------------------------------------------------------------------------
    | Release Information
    |--------------------------------------------------------------------------
    |
    | Additional release metadata.
    |
    */

    'release_date' => '2025-11-04',
    'codename' => 'Phoenix',

];
