<?php

namespace RyanBadger\LaravelAdmin\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use RyanBadger\LaravelAdmin\Middleware\CheckCmsAccess;

class AdminModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes from the package
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        // Load views from the package with a namespace
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'laravel-admin');

        // Register middleware for use within the package
        $this->app['router']->aliasMiddleware('cms.access', CheckCmsAccess::class);

        // Set Bootstrap as the default pagination style for any paginated views
        Paginator::useBootstrap();

        // Load migrations directly from the package
        $this->loadMigrationsFrom(__DIR__.'/../../src/Migrations');

        // Optionally publish assets if customization is required
        $this->publishes([
            __DIR__.'/../../resources/assets' => public_path('vendor/laravel-admin'),
        ], 'laravel-admin-assets');

        // You can still offer the publishing option for migrations in case modifications are needed
        $this->publishes([
            __DIR__.'/../../src/Migrations' => database_path('migrations')
        ], 'laravel-admin-migrations');
    }
}
