<?php

namespace RyanBadger\LaravelAdmin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
        $tableName = $modelInstance->getTable();
        $columns = Schema::getColumnListing($tableName); // Get all columns

        $fields = [];
        foreach ($columns as $columnName) {
            $type = Schema::getColumnType($tableName, $columnName);

            // Optionally, add custom handling for specific types like 'enum'
            if ($type === 'enum') {
                $columnDetails = DB::select(DB::raw("SHOW COLUMNS FROM {$tableName} WHERE Field = '{$columnName}'"));
                $type = $columnDetails[0]->Type; // This will include the enum values e.g., enum('value1','value2')
            }

            $fields[$columnName] = [
                'type' => $type,
                'editable' => in_array($columnName, $modelInstance->getFillable()), // Determine editability
                'length' => null, // Length might not be relevant for all types
                'nullable' => DB::select(DB::raw("SELECT IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$tableName}' AND COLUMN_NAME = '{$columnName}'"))[0]->IS_NULLABLE === 'YES',
                'show_in_list' => in_array($columnName, $modelInstance->getFillable()), // Show in list if fillable
            ];
        }

        return $fields;
    }






}
