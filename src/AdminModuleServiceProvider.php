<?php 

namespace RyanBadger\LaravelAdmin;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class AdminModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        \Log::info('AdminModuleServiceProvider booting...');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-admin');

        $this->publishes([
            __DIR__.'/../config/admin_module.php' => config_path('admin_module.php'),
        ], 'laravel-admin-config');

        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/laravel-admin'),
        ], 'laravel-admin-assets');

        $this->mergeConfigFrom(__DIR__.'/../config/admin_module.php', 'admin_module');

        $this->registerModelInformation();
    }

    public function register()
    {
        $this->commands([
            Console\CreateModelConfigCommand::class,
        ]);
    }

    protected function registerModelInformation()
    {
        if (!cache()->has('admin_models')) {
            $modelPaths = File::allFiles(app_path('Models'));
            $models = [];

            foreach ($modelPaths as $modelPath) {
                $model = $this->getModelClassName($modelPath);
                if (!class_exists($model)) {
                    continue;
                }

                $tableName = (new $model)->getTable();
                if (!Schema::hasTable($tableName)) {
                    continue;
                }

                $fields = $this->getModelFields($tableName);
                // Use a simple slug as the key
                $slug = strtolower(class_basename($model));
                $models[$slug] = [
                    'class' => $model,
                    'fields' => $fields
                ];
                \Log::info("Registering model: $model with slug $slug");
            }

            cache()->forever('admin_models', $models);
        }
    }






    protected function getModelClassName($modelPath)
    {
        $path = $modelPath->getRelativePathName();
        $class = sprintf('\App\Models\%s', strtr(substr($path, 0, strrpos($path, '.')), '/', '\\'));
        return $class;
    }

    protected function getModelFields($tableName)
    {
        $columns = Schema::getColumnListing($tableName);
        $fields = [];

        foreach ($columns as $column) {
            $type = Schema::getColumnType($tableName, $column);
            $fields[$column] = $type;
        }

        return $fields;
    }
}
