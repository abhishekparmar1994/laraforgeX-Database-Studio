<?php

declare(strict_types=1);

namespace Laraforge\DatabaseStudio;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laraforge\DatabaseStudio\Agents\DatabaseManagerAgent;

class DatabaseStudioServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap package services, views, and routes.
     */
    public function boot(): void
    {
        if (!config('database-studio.enabled', true)) {
            return;
        }

        $this->registerPublishing();
        $this->registerViews();
        $this->registerRoutes();
    }

    /**
     * Register package services and singleton bindings.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/database-studio.php',
            'database-studio'
        );

        $this->app->singleton(DatabaseManagerAgent::class, function ($app) {
            $connection = config('database-studio.connection');
            return new DatabaseManagerAgent($connection);
        });
    }

    /**
     * Register view namespace for database-studio::*.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views/database-studio', 'database-studio');
    }

    /**
     * Register package Web and API routes.
     */
    protected function registerRoutes(): void
    {
        // Register Web GUI Routes
        Route::group([
            'prefix'     => config('database-studio.path', 'database-studio'),
            'middleware' => config('database-studio.middleware.web', ['web']),
            'as'         => 'database-studio.',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });

        // Register API Routes (includes 'web' for session cookie support)
        $apiMiddleware = config('database-studio.middleware.api', ['web', 'api']);
        if (!in_array('web', (array) $apiMiddleware, true)) {
            $apiMiddleware = array_merge(['web'], (array) $apiMiddleware);
        }

        Route::group([
            'prefix'     => config('database-studio.api_prefix', 'api/v1/database-manager'),
            'middleware' => $apiMiddleware,
            'as'         => 'database-studio.api.',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }

    /**
     * Register publishable resources (config, views, assets).
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/database-studio.php' => config_path('database-studio.php'),
            ], 'database-studio-config');

            $this->publishes([
                __DIR__ . '/../resources/views/database-studio' => resource_path('views/vendor/database-studio'),
            ], 'database-studio-views');
        }
    }
}
