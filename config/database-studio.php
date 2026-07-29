<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Database Studio Status
    |--------------------------------------------------------------------------
    | Enable or disable the Database Studio package GUI and API routes.
    */
    'enabled' => env('DB_STUDIO_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Web Dashboard Path & Prefix
    |--------------------------------------------------------------------------
    | The URL path where the Database Studio Web GUI dashboard is hosted.
    */
    'path' => env('DB_STUDIO_PATH', 'database-studio'),

    /*
    |--------------------------------------------------------------------------
    | API Prefix
    |--------------------------------------------------------------------------
    | The prefix for API endpoints called by the Web GUI and external clients.
    */
    'api_prefix' => env('DB_STUDIO_API_PREFIX', 'api/v1/database-manager'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Security Settings
    |--------------------------------------------------------------------------
    | Require username/email & password login before accessing Database Studio.
    | Credentials can be customized via .env or in this config file.
    */
    'auth' => [
        'enabled'  => env('DB_STUDIO_AUTH_ENABLED', true),
        'username' => env('DB_STUDIO_AUTH_USERNAME', 'admin@admin.com'),
        'password' => env('DB_STUDIO_AUTH_PASSWORD', 'admin123'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    | The middleware assigned to Database Studio web and API routes.
    | 'web' middleware is included for API routes to enable session auth & cookies.
    */
    'middleware' => [
        'web' => ['web'],
        'api' => ['web', 'api'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    | The database connection used by Database Studio.
    | Set to null to automatically inherit host app's active .env database credentials.
    */
    'connection' => env('DB_STUDIO_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | Safety & Security Protections
    |--------------------------------------------------------------------------
    | Prevent destructive actions (drop/truncate) on critical system tables.
    */
    'protected_tables' => [
        'migrations',
        'failed_jobs',
        'personal_access_tokens',
        'password_reset_tokens',
    ],
];
