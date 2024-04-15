<?php

namespace RyanBadger\LaravelAdmin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function dashboard()
    {
        $models = config('admin_module.models');  // Assuming 'models' contains all the models you want to manage
        $modelData = [];

        foreach ($models as $key => $value) {
            $modelClass = $value['class'];
            if (class_exists($modelClass)) {
                $modelData[$key] = [
                    'count' => $modelClass::count(),
                    'name' => Str::plural(ucfirst($key))
                ];
            }
        }

        return view('laravel-admin::admin.dashboard', compact('modelData'));
    }


    public function settings()
    {
        return view('laravel-admin::admin.settings');
    }
    
    public function index($model)
    {
        $models = config('admin_module'); // Fetch configuration
        $modelKey = '_app_models_' . $model; // Adjust based on how keys are formatted in your config

        if (!isset($models[$modelKey])) {
            abort(404, 'Model not found');
        }

        $modelClass = $models[$modelKey]['class'];
        $fields = $models[$modelKey]['fields'];
        $records = $modelClass::all();

        return view('laravel-admin::admin.index', compact('records', 'model', 'fields'));
    }

    public function store(Request $request, $model)
    {
        $modelClass = $this->getModelClass($model);
        $validationRules = $this->getValidationRules($model);
        $validatedData = $request->validate($validationRules);
        $record = $modelClass::create($validatedData);
        return redirect()->route('admin.index', $model)->with('success', 'Record created successfully!');
    }


    public function create($model)
    {
        $modelClass = $this->getModelClass($model);
        $fields = $this->getModelFields($modelClass);
        return view('laravel-admin::admin.form', compact('model', 'fields'));
    }

    public function edit($model, $id)
    {
        $modelClass = $this->getModelClass($model);
        $record = $modelClass::find($id);
        if (!$record) {
            return redirect()->route('admin.index', $model)->withErrors('Record not found.');
        }
        $fields = $this->getModelFields($modelClass);
        return view('laravel-admin::admin.form', compact('model', 'record', 'fields'));
    }

    public function update(Request $request, $model, $id)
    {
        $modelClass = $this->getModelClass($model);
        $record = $modelClass::findOrFail($id);

        // Generate validation rules based on model fields
        $fields = $this->getModelFields($modelClass);
        $validationRules = $this->getValidationRules($fields);
        $validatedData = $request->validate($validationRules);

        $record->update($validatedData);
        return redirect()->route('admin.index', $model)->with('success', 'Record updated successfully!');
    }




    public function destroy($model, $id)
    {
        $modelClass = $this->getModelClass($model);
        $record = $modelClass::findOrFail($id);
        $record->delete();
        return redirect()->route('admin.index', $model)->with('success', 'Record deleted successfully!');
    }


    private function getModelClass($model)
    {
        $normalizedModelName = '\\App\\Models\\' . Str::studly($model);
        if (!class_exists($normalizedModelName)) {
            abort(404, "Model class {$normalizedModelName} not found.");
        }
        return $normalizedModelName;
    }

    private function getModelFields($modelClass)
    {
        $tableName = (new $modelClass)->getTable();
        if (!Schema::hasTable($tableName)) {
            abort(404, "Table for model {$modelClass} does not exist.");
        }
        $columns = Schema::getColumnListing($tableName);
        $fields = [];
        foreach ($columns as $column) {
            $type = Schema::getColumnType($tableName, $column);
            $fields[$column] = $type;
        }
        return $fields;
    }


    private function getValidationRules($fields)
    {
        $rules = [];
        foreach ($fields as $field => $type) {
            switch ($type) {
                case 'integer':
                    $rules[$field] = 'integer';
                    break;
                case 'text':
                case 'varchar':
                    $rules[$field] = 'string|max:255'; // Customize the max value as needed
                    break;
                default:
                    $rules[$field] = '';
            }
        }
        return $rules;
    }


}