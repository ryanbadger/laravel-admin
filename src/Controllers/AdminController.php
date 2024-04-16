<?php

namespace RyanBadger\LaravelAdmin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // Helper to get the model configuration and handle missing model error
    private function getModelConfig($slug)
    {
        $modelConfig = config("admin_module.models.$slug");

        if (!$modelConfig) {
            abort(404, 'Model not found');
        }

        return $modelConfig;
    }

    public function dashboard()
    {
        $models = config('admin_module.models');
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
        $modelConfig = $this->getModelConfig($slug);
        $records = $modelConfig['class']::all();
        return view('laravel-admin::admin.index', compact('records', 'slug', 'modelConfig'));
    }

    public function create($slug)
    {
        $modelConfig = $this->getModelConfig($slug);
        return view('laravel-admin::admin.form', compact('slug', 'modelConfig'));
    }

    public function edit($slug, $id)
    {
        $modelConfig = $this->getModelConfig($slug);
        $record = $modelConfig['class']::find($id);

        if (!$record) {
            return redirect()->route('admin.index', $slug)->withErrors('Record not found.');
        }

        return view('laravel-admin::admin.form', compact('slug', 'record', 'modelConfig'));
    }

    public function store(Request $request, $slug)
    {
        $modelConfig = $this->getModelConfig($slug);
        $validationRules = $this->generateValidationRules($modelConfig['fields']);
        $validatedData = $request->validate($validationRules);
        $modelConfig['class']::create($validatedData);
        return redirect()->route('admin.index', $slug)->with('success', 'Record created successfully!');
    }

    public function update(Request $request, $slug, $id)
    {
        $modelConfig = $this->getModelConfig($slug);
        $record = $modelConfig['class']::findOrFail($id);
        $validationRules = $this->generateValidationRules($modelConfig['fields']);
        $validatedData = $request->validate($validationRules);
        $record->update($validatedData);
        return redirect()->route('admin.index', $slug)->with('success', 'Record updated successfully!');
    }


    public function destroy($slug, $id)
    {
        $modelConfig = $this->getModelConfig($slug);
        $record = $modelConfig['class']::findOrFail($id);
        $record->delete();
        return redirect()->route('admin.index', $slug)->with('success', 'Record deleted successfully!');
    }

    private function generateValidationRules($fields)
    {
        $rules = [];
        foreach ($fields as $field => $details) {
            if (!$details['editable']) {
                continue;  // Skip fields that are not editable
            }

            // Start constructing the rule
            $ruleParts = [];

            // Add 'required' or 'nullable' based on some condition if needed
            // Assuming you have a key to determine if the field is required
            $ruleParts[] = isset($details['required']) && $details['required'] ? 'required' : 'nullable';

            // Add type-specific rules
            if ($details['type'] === 'integer' || $details['type'] === 'tinyint') {
                $ruleParts[] = 'integer';
            } else {
                $ruleParts[] = 'string';
            }

            // Add max length if applicable
            if (!empty($details['length'])) {
                $ruleParts[] = "max:{$details['length']}";
            }

            // Join all parts to form the complete rule
            $rules[$field] = implode('|', $ruleParts);
        }
        return $rules;
    }



}
