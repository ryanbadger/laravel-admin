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
        $config = $this->initialAdminModuleConfig(); // Load initial or existing config

        foreach ($modelPaths as $path) {
            $modelClass = $this->getModelClassName($path);
            if (!class_exists($modelClass)) {
                $this->error("Skipped: Class $modelClass does not exist.");
                continue;
            }

            $modelInstance = new $modelClass();
            if (!Schema::hasTable($modelInstance->getTable())) {
                $this->error("Skipped: Table for model $modelClass does not exist.");
                continue;
            }

            $fields = $this->getModelFields($modelInstance);
            $slug = Str::snake(class_basename($modelClass));

            $config['models'][$slug] = [
                'class' => $modelClass,
                'fields' => $fields,
            ];
            $this->info("Processed: $modelClass");
        }

        $path = config_path('admin_module.php');
        File::put($path, '<?php return ' . var_export($config, true) . ';');
        $this->info('Model configuration generated/updated successfully.');
    }



    protected function initialAdminModuleConfig()
    {
        // Load existing configuration if available
        $defaultConfig = [
            'models' => [],
            'access_emails' => ['admin@example.com'], // Default admin access
        ];
        if (file_exists(config_path('admin_module.php'))) {
            return include config_path('admin_module.php');
        }
        return $defaultConfig;
    }


    protected function getModelClassName($modelPath)
    {
        $path = $modelPath->getRelativePathName();
        $classNameWithExtension = substr($path, 0, strrpos($path, '.'));
        $className = strtr($classNameWithExtension, '/', '\\');
        return '\\App\\Models\\' . $className;
    }

    protected function getModelFields($modelClass)
    {
        $modelInstance = new $modelClass; // Create an instance of the model
        $columns = Schema::getColumnListing($modelInstance->getTable()); // Get all columns

        $fields = [];
        foreach ($columns as $columnName) {
            $columnDetails = Schema::getConnection()->getDoctrineColumn($modelInstance->getTable(), $columnName);
            $type = Schema::getColumnType($modelInstance->getTable(), $columnName);
            $fields[$columnName] = [
                'type' => $type,
                'editable' => in_array($columnName, $modelInstance->getFillable()), // Determine editability
                'length' => $columnDetails->getLength(),
                'nullable' => !$columnDetails->getNotnull(), // Determine if the column is nullable
                'show_in_list' => in_array($columnName, $modelInstance->getFillable()), // Show in list if fillable
            ];
        }

        return $fields;
    }





}
