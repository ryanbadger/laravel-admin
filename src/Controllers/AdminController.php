<?php

namespace RyanBadger\LaravelAdmin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    public function dashboard()
    {
        $modelClasses = $this->getAllModelClasses();
        $modelData = [];

        foreach ($modelClasses as $modelClass) {
            $slug = Str::snake(class_basename($modelClass));
            $modelData[$slug] = [
                'count' => $modelClass::count(),
                'name' => Str::plural(class_basename($modelClass))
            ];
        }

        return view('laravel-admin::admin.dashboard', compact('modelData'));
    }

    public function settings()
    {
        return view('laravel-admin::admin.settings');
    }

    public function index(Request $request, $slug)
    {
        $modelClass = $this->getModelClass($slug);
        $modelInstance = new $modelClass();

        if (!method_exists($modelInstance, 'cmsFields')) {
            abort(404, 'Model not found');
        }

        $fields = $this->getModelFields($modelInstance);

        $query = $modelClass::query();

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($query) use ($fields, $searchTerm) {
                foreach ($fields as $field => $fieldDetails) {
                    if (isset($fieldDetails['searchable']) && $fieldDetails['searchable']) {
                        $query->orWhere($field, 'like', "%{$searchTerm}%");
                    }
                }
            });
        }

        if ($request->has('sort') && !empty($request->sort)) {
            $sortDirection = $request->get('direction', 'asc');
            $query->orderBy($request->sort, $sortDirection);
        }

        $records = $query->paginate(25);

        return view('laravel-admin::admin.index', compact('records', 'slug', 'fields'));
    }

    public function create($slug)
    {
        $modelClass = $this->getModelClass($slug);
        $modelInstance = new $modelClass();

        $fields = $this->getModelFields($modelInstance);
        

        return view('laravel-admin::admin.form', compact('slug', 'fields'));
    }

    public function edit($slug, $id)
    {
        $modelClass = $this->getModelClass($slug);
        $record = $modelClass::findOrFail($id);

        $fields = $this->getModelFields($record);

        // Prepare field values including nested data fields
        $fieldValues = [];
        foreach ($fields as $field => $attributes) {
            if (Str::startsWith($field, 'data[')) {
                $key = Str::between($field, 'data[', ']');
                $fieldValues[$field] = old($field, $record->data[$key] ?? null);
            } else {
                $fieldValues[$field] = old($field, $record->$field);
            }
        }

        return view('laravel-admin::admin.form', compact('slug', 'record', 'fields', 'fieldValues'));
    }


    public function store(Request $request, $slug)
    {
        $modelClass = $this->getModelClass($slug);
        $modelInstance = new $modelClass();

        $fields = $this->getModelFields($modelInstance);

        $validationRules = $this->generateValidationRules($fields);
        $validatedData = $request->validate($validationRules);

        // Manually handle the nested JSON data if present
        $dataFields = $request->input('data', []);
        if (!empty($dataFields)) {
            $validatedData['data'] = $dataFields; // directly assign validated data to the validatedData array
        }

        $modelInstance->fill($validatedData);
        $modelInstance->save();

        // redirect to the edit page
        return redirect()->route('admin.edit', ['slug' => $slug, 'id' => $modelInstance->id])->with('success', 'Record created successfully!');
    }

    public function update(Request $request, $slug, $id)
    {
        $modelClass = $this->getModelClass($slug);
        $record = $modelClass::findOrFail($id);
        $fields = $this->getModelFields($record);

        // Generate validation rules dynamically, including for data fields
        $validationRules = $this->generateValidationRules($fields);
        $validatedData = $request->validate($validationRules);

        // Manually handle the nested JSON data if present
        $dataFields = $request->input('data', []);
        if (!empty($dataFields)) {
            $record->data = $dataFields; // directly assign validated data
        }

        // Update other fields
        $record->fill($validatedData);
        $record->save();
        
        return back()->with('success', 'Record updated successfully!');
    }


    public function destroy($slug, $id)
    {
        $modelClass = $this->getModelClass($slug);
        $record = $modelClass::findOrFail($id);
        $record->delete();

        return redirect()->route('admin.index', $slug)->with('success', 'Record deleted successfully!');
    }

    protected function getModelClass($slug)
    {
        // Check if the model class exists in the default namespace
        $defaultNamespace = 'App\\Models\\';
        $modelClass = $defaultNamespace . Str::studly($slug); // Ensure the class name is in StudlyCase

        if (class_exists($modelClass)) {
            return $modelClass;
        }

        // If not found, check in the package namespace
        $packageNamespace = 'RyanBadger\\LaravelAdmin\\Models\\';
        $modelClass = $packageNamespace . Str::studly($slug); // Ensure the class name is in StudlyCase

        if (class_exists($modelClass)) {
            return $modelClass;
        }

        // Handle the case where the model is not found
        abort(404, 'Model not found');
    }

    protected function getModelFields($modelInstance)
    {
        $fields = [];

        if (method_exists($modelInstance, 'cmsFields')) {
            $fields = $modelInstance->cmsFields();
        }

        return $fields;
    }

    protected function generateValidationRules($fields)
    {
        $rules = [];
        foreach ($fields as $field => $fieldDetails) {
            if (!$fieldDetails['editable']) {
                continue;
            }

            $baseRule = $fieldDetails['required'] ?? false ? 'required' : 'nullable';

            // Handle nested data fields dynamically
            if (Str::startsWith($field, 'data[')) {
                $rules[$field] = "$baseRule|{$fieldDetails['type']}"; // Adjust based on the field type, e.g., string, integer
                if (isset($fieldDetails['options'])) {
                    $rules[$field] .= '|in:' . implode(',', array_keys($fieldDetails['options']));
                }
            } else {
                // Normal field processing
                $rules[$field] = $this->processValidationRule($fieldDetails);
            }
        }
        return $rules;
    }

    private function processValidationRule($details)
    {
        $rule = $details['required'] ? 'required' : 'nullable';
        switch ($details['type']) {
            case 'text':
            case 'textarea':
            case 'select':
                $rule .= '|string';
                break;
            case 'checkbox':
                $rule .= '|boolean';
                break;
            case 'number':
                $rule .= '|integer';
                break;
            case 'date':
                $rule .= '|date';
                break;
            // Add other types as needed
        }
        if (isset($details['options'])) {
            $rule .= '|in:' . implode(',', array_keys($details['options']));
        }
        return $rule;
    }


    protected function getAllModelClasses()
    {
        $modelClasses = [];
        $modelsPath = app_path('Models');
        $modelFiles = File::allFiles($modelsPath);

        foreach ($modelFiles as $modelFile) {
            $modelClass = 'App\\Models\\' . $modelFile->getBasename('.php');
            if (is_subclass_of($modelClass, Model::class) && method_exists($modelClass, 'cmsFields')) {
                $modelClasses[] = $modelClass;
            }
        }

        // Explicitly add the Media model from the package if it has the cmsFields method
        if (method_exists(\RyanBadger\LaravelAdmin\Models\Media::class, 'cmsFields')) {
            $modelClasses[] = \RyanBadger\LaravelAdmin\Models\Media::class;
        }

        return $modelClasses;
    }
}
