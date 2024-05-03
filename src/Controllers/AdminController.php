<?php

namespace RyanBadger\LaravelAdmin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use ReflectionClass;
use ReflectionMethod;

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
                    if ($fieldDetails['type'] !== 'relation' && isset($fieldDetails['searchable']) && $fieldDetails['searchable']) {
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


    public function relationSearch(Request $request, $slug, $field)
    {
        $modelClass = $this->getModelClass($slug);
        $fields = $this->getModelFields(new $modelClass());
        $fieldDetails = $fields[$field];

        if ($fieldDetails['type'] !== 'relation' || $fieldDetails['relation_type'] !== 'BelongsTo') {
            abort(400, 'Invalid field for relation search.');
        }

        $relatedModelClass = $fieldDetails['related_model'];
        $relatedFields = $this->getModelFields(new $relatedModelClass());
        $searchableFields = array_filter($relatedFields, function($fieldDetails) {
            return $fieldDetails['type'] !== 'relation';
        });

        $searchTerm = $request->input('search', '');
        $query = $relatedModelClass::query();

        if (!empty($searchableFields)) {
            $query->where(function($q) use ($searchableFields, $searchTerm) {
                foreach ($searchableFields as $field => $fieldDetails) {
                    $q->orWhere($field, 'like', '%' . $searchTerm . '%');
                }
            });
        }

        $results = $query->take(10)
                        ->get()
                        ->map(function ($item) use ($searchableFields) {
                            return [
                                'id' => $item->getKey(),
                                'text' => $item->{array_key_first($searchableFields)}
                            ];
                        });

        return response()->json($results);
    }

    public function create($slug)
    {
        $modelClass = $this->getModelClass($slug);
        $modelInstance = new $modelClass();

        $fields = $this->getModelFields($modelInstance);

        $selectOptions = $this->getDynamicSelectOptions($fields);

        return view('laravel-admin::admin.form', compact('slug', 'fields', 'selectOptions'));
    }

    public function edit($slug, $id)
    {
        $modelClass = $this->getModelClass($slug);
        $record = $modelClass::findOrFail($id);

        $fields = $this->getModelFields($record);

        $selectOptions = $this->getDynamicSelectOptions($fields, $record);

        return view('laravel-admin::admin.form', compact('slug', 'record', 'fields', 'selectOptions'));
    }

    public function store(Request $request, $slug)
    {
        $modelClass = $this->getModelClass($slug);
        $modelInstance = new $modelClass();

        $fields = $this->getModelFields($modelInstance);

        $validationRules = $this->generateValidationRules($fields);
        $validatedData = $request->validate($validationRules);

        $record = $modelClass::create($validatedData);

        foreach ($fields as $field => $attributes) {
            if ($attributes['type'] === 'relation') {
                if ($attributes['relation_type'] === 'BelongsTo') {
                    $relatedModel = $attributes['related_model'];
                    $relatedId = $validatedData[$field] ?? null;
                    
                    if ($relatedId) {
                        $relatedRecord = $relatedModel::find($relatedId);
                        $record->{$field}()->associate($relatedRecord);
                    }
                } elseif ($attributes['relation_type'] === 'HasMany') {
                    $relatedData = $validatedData[$field] ?? [];
                    
                    foreach ($relatedData as $relatedRecord) {
                        $record->{$field}()->create($relatedRecord);
                    }
                }
            }
        }

        $record->save();

        return redirect()->route('admin.index', $slug)->with('success', 'Record created successfully!');
    }

    public function update(Request $request, $slug, $id)
    {
        $modelClass = $this->getModelClass($slug);
        $record = $modelClass::findOrFail($id);

        $fields = $this->getModelFields($record);

        $validationRules = $this->generateValidationRules($fields);
        $validatedData = $request->validate($validationRules);

        $record->fill($validatedData);

        foreach ($fields as $field => $attributes) {
            if ($attributes['type'] === 'relation') {
                if ($attributes['relation_type'] === 'BelongsTo') {
                    $relatedModel = $attributes['related_model'];
                    $relatedId = $validatedData[$field] ?? null;
                    
                    if ($relatedId) {
                        $relatedRecord = $relatedModel::find($relatedId);
                        $record->{$field}()->associate($relatedRecord);
                    } else {
                        $record->{$field}()->dissociate();
                    }
                } elseif ($attributes['relation_type'] === 'HasMany') {
                    $relatedData = $validatedData[$field] ?? [];
                    
                    $record->{$field}()->sync($relatedData);
                }
            }
        }

        $record->save();

        return redirect()->route('admin.index', $slug)->with('success', 'Record updated successfully!');
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


    protected function getDynamicSelectOptions($fields, $record = null)
    {
        $selectOptions = [];

        foreach ($fields as $field => $fieldDetails) {
            if ($fieldDetails['type'] === 'relation' && $fieldDetails['relation_type'] === 'BelongsTo') {
                $relatedModelClass = $fieldDetails['related_model'];
                // $selectOptions[$field] = $relatedModelClass::pluck('name', 'id');
                $selectOptions[$field] = $relatedModelClass::pluck('title', 'id');
            }
        }

        return $selectOptions;
    }

    protected function generateValidationRules($fields)
    {
        $rules = [];
        foreach ($fields as $field => $fieldDetails) {
            if (!$fieldDetails['editable']) {
                continue;
            }

            $ruleParts = $fieldDetails['required'] ?? false ? ['required'] : ['nullable'];

            switch ($fieldDetails['type']) {
                case 'text':
                case 'textarea':
                case 'select':
                    $ruleParts[] = 'string';
                    break;
                case 'checkbox':
                    $ruleParts[] = 'boolean';
                    break;
                case 'number':
                    $ruleParts[] = 'integer';
                    break;
                case 'date':
                    $ruleParts[] = 'date';
                    break;
                case 'datetime-local':
                    $ruleParts[] = 'date_format:Y-m-d\TH:i:s';
                    break;
                default:
                    $ruleParts[] = 'string';
                    break;
            }

            if (isset($fieldDetails['options'])) {
                $ruleParts[] = 'in:' . implode(',', array_keys($fieldDetails['options']));
            }

            $rules[$field] = implode('|', $ruleParts);
        }
        return $rules;
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


    protected function convertColumnTypeToHtmlType($columnType)
    {
        switch ($columnType) {
            case 'string':
            case 'varchar':
            case 'char':
                return 'text';
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'textarea';
            case 'boolean':
            case 'tinyint':
                return 'checkbox';
            case 'integer':
            case 'bigint':
            case 'smallint':
                return 'number';
            case 'date':
                return 'date';
            case 'datetime':
            case 'timestamp':
                return 'datetime-local';
            default:
                return 'text';
        }
    }
}