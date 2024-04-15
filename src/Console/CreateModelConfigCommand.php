<?php

namespace RyanBadger\LaravelAdmin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateModelConfigCommand extends Command
{
    protected $signature = 'admin:generate-model-config';
    protected $description = 'Generate or update the model configuration for the admin module';

    public function handle()
    {
        $modelPaths = File::allFiles(app_path('Models'));
        $config = [];

        foreach ($modelPaths as $path) {
            $modelClass = $this->getModelClassName($path);
            if (!class_exists($modelClass)) {
                continue;
            }

            $tableName = (new $modelClass)->getTable();
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            $fields = $this->getModelFields($tableName);
            $config[str_replace('\\', '', Str::snake($modelClass))] = [
                'class' => $modelClass,
                'fields' => $fields,
            ];
        }

        $path = config_path('admin_module.php');
        File::put($path, '<?php return ' . var_export($config, true) . ';');
        $this->info('Model configuration generated/updated successfully.');
    }

    protected function getModelClassName($modelPath)
    {
        $path = $modelPath->getRelativePathName();
        return '\\App\\Models\\' . strtr(substr($path, 0, strrpos($path, '.')), '/', '\\');
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
