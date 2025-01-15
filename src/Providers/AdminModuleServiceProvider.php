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

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Register middleware for use within the package
        $this->app['router']->aliasMiddleware('cms.access', CheckCmsAccess::class);

        // Set Bootstrap as the default pagination style for any paginated views
        Paginator::useBootstrap();

        // Publish views for customization
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/laravel-admin'),
        ], 'laravel-admin-views');

        // Publish assets
        $this->publishes([
            __DIR__.'/../../resources/assets' => public_path('vendor/laravel-admin'),
        ], 'laravel-admin-assets');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'laravel-admin-migrations');

        // Read the composer.json file of the package
        $composerJson = file_get_contents(__DIR__.'/../../composer.json');
        $composerConfig = json_decode($composerJson, true);

        // Share the package version with all views
        view()->share('laravel_admin_version', $composerConfig['version']);
    }

    public function register()
    {
        //
    }
}
