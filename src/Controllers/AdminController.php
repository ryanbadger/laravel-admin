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
        $models = cache('admin_models'); // Fetch the model data from cache
        $modelData = [];

        foreach ($models as $slug => $details) {
            $modelClass = $details['class'];
            if (class_exists($modelClass)) {
                $modelData[$slug] = [
                    'count' => $modelClass::count(),
                    'name' => Str::plural(class_basename($modelClass))
                ];
            }
        }

        return view('laravel-admin::admin.dashboard', compact('modelData'));
    }

    public function settings()
    {
        return view('laravel-admin::admin.settings');
    }
    
    public function index($slug)
    {
        $models = cache('admin_models');
        if (!isset($models[$slug])) {
            abort(404, 'Model not found');
        }

        $modelClass = $models[$slug]['class'];
        $fields = $models[$slug]['fields'];
        $records = $modelClass::all();

        return view('laravel-admin::admin.index', compact('records', 'slug', 'fields'));
    }

    public function store(Request $request, $slug)
    {
        $models = cache('admin_models');
        if (!isset($models[$slug])) {
            abort(404, 'Model not found');
        }

        $modelClass = $models[$slug]['class'];
        $fields = $models[$slug]['fields'];
        $validationRules = $this->getValidationRules($fields);
        $validatedData = $request->validate($validationRules);
        $record = $modelClass::create($validatedData);

        return redirect()->route('admin.index', $slug)->with('success', 'Record created successfully!');
    }

    public function create($slug)
    {
        $modelClass = $this->getModelClass($slug);
        $fields = $this->getModelFields($modelClass);
        return view('laravel-admin::admin.form', compact('slug', 'fields'));
    }

    public function edit($slug, $id)
    {
        $modelClass = $this->getModelClass($slug);
        $record = $modelClass::find($id);
        if (!$record) {
            return redirect()->route('admin.index', $slug)->withErrors('Record not found.');
        }
        $fields = $this->getModelFields($modelClass);
        return view('laravel-admin::admin.form', compact('slug', 'record', 'fields'));
    }

    public function update(Request $request, $slug, $id)
    {
        $modelClass = $this->getModelClass($slug);
        $record = $modelClass::findOrFail($id);

        $fields = $this->getModelFields($modelClass);
        $validationRules = $this->generateValidationRules($fields);

        $validatedData = $request->validate($validationRules);

        $record->update($validatedData);
        return redirect()->route('admin.index', $slug)->with('success', 'Record updated successfully!');
    }



    public function destroy($slug, $id)
    {
        $modelClass = $this->getModelClass($slug);
        $record = $modelClass::findOrFail($id);
        $record->delete();
        return redirect()->route('admin.index', $slug)->with('success', 'Record deleted successfully!');
    }

    private function getModelClass($slug)
{
    $models = cache('admin_models');
    if (!isset($models[$slug]) || !class_exists($models[$slug]['class'])) {
        abort(404, "Model class for {$slug} not found.");
    }
    return $models[$slug]['class'];
}

private function getModelFields($modelClass)
{
    $tableName = (new $modelClass)->getTable();
    $columns = Schema::getColumnListing($tableName);
    $fields = [];

    foreach ($columns as $column) {
        $columnType = Schema::getColumnType($tableName, $column);
        $columnDetails = Schema::getConnection()->getDoctrineColumn($tableName, $column);
        $length = $columnDetails->getLength();
        // Corrected the condition here
        $nullable = $columnDetails->getNotnull() ? 'required' : 'nullable';

        $fields[$column] = [
            'type' => $columnType,
            'length' => $length,
            'nullable' => $nullable  // Correct usage of nullable or required
        ];
    }

    return $fields;
}


private function generateValidationRules($fields)
{
    $rules = [];
    foreach ($fields as $field => $details) {
        if ($field === 'id' || strpos($field, '_at') !== false) {
            continue;  // Skip 'id' and timestamp fields
        }

        $typeRule = ($details['type'] === 'integer' || $details['type'] === 'tinyint') ? 'integer' : 'string';
        $lengthRule = $details['length'] ? "|max:{$details['length']}" : '';
        $rules[$field] = "{$details['nullable']}|$typeRule$lengthRule";
    }
    return $rules;
}


}
