<?php 

namespace RyanBadger\LaravelAdmin;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RyanBadger\LaravelAdmin\Middleware\CheckCmsAccess;
use RyanBadger\LaravelAdmin\Console\CreateModelConfigCommand;

class AdminModuleServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->commands([
            Console\CreateModelConfigCommand::class,
        ]);
    }

    public function boot()
    {
        \Log::info('AdminModuleServiceProvider booting...');


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

        $this->app['router']->aliasMiddleware('cms.access', CheckCmsAccess::class);

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
