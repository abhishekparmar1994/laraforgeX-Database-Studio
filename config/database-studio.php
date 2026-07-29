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
    | Route Middleware
    |--------------------------------------------------------------------------
    | The middleware assigned to Database Studio web and API routes.
    */
    'middleware' => [
        'web' => ['web'],
        'api' => ['api'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    | The database connection used by Database Studio (null defaults to default connection).
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
