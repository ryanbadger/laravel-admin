<?php

namespace RyanBadger\LaravelAdmin;

use Illuminate\Support\ServiceProvider;
use RyanBadger\LaravelAdmin\Console\CreateModelConfigCommand;
use RyanBadger\LaravelAdmin\Middleware\CheckCmsAccess;

class AdminModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            CreateModelConfigCommand::class,
        ]);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-admin');
        
        $this->publishes([
            __DIR__.'/../config/admin_module.php' => config_path('admin_module.php'),
        ], 'laravel-admin-config');

        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/laravel-admin'),
        ], 'laravel-admin-assets');

        $this->mergeConfigFrom(__DIR__.'/../config/admin_module.php', 'admin_module');

        $this->app['router']->aliasMiddleware('cms.access', CheckCmsAccess::class);
    }
}
