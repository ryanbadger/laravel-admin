<?php 

namespace RyanBadger\LaravelAdmin;

use Illuminate\Support\ServiceProvider;

class AdminModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../views', 'adminmodule');
        $this->publishes([
            __DIR__.'/../config/admin_module.php' => config_path('admin_module.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/admin_module.php', 'admin_module');
    }
}
