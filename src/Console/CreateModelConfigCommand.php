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
        // Load existing configuration, or start with a default structure if not present
        $existingConfig = config('admin_module', ['models' => [], 'access_emails' => ['admin@example.com']]);

        $config = [
            'models' => [],
            'access_emails' => $existingConfig['access_emails'], // Preserve existing access emails
        ];

        $modelPaths = File::allFiles(app_path('Models'));

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


    protected function getModelClassName($modelPath)
    {
        $path = $modelPath->getRelativePathName();
        $classNameWithExtension = substr($path, 0, strrpos($path, '.'));
        $className = strtr($classNameWithExtension, '/', '\\');
        return '\\App\\Models\\' . $className;
    }

    protected function getModelFields($modelInstance)
    {
        $tableName = $modelInstance->getTable();
        $columns = Schema::getColumnListing($tableName);
        $fields = [];

        foreach ($columns as $columnName) {
            $fields[$columnName] = [
                'type' => Schema::getColumnType($tableName, $columnName),
                'editable' => in_array($columnName, $modelInstance->getFillable()),
                'nullable' => !in_array($columnName, $modelInstance->getGuarded()),
                'show_in_list' => in_array($columnName, $modelInstance->getFillable()),
            ];
        }

        return $fields;
    }


}
