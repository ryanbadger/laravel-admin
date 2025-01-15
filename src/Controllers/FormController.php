<?php

namespace RyanBadger\LaravelAdmin\Controllers;

use Illuminate\Http\Request;
use RyanBadger\LaravelAdmin\Models\Form;
use RyanBadger\LaravelAdmin\Models\FormField;

class FormController extends AdminController
{
    protected string $model = Form::class;
    protected string $routePrefix = 'forms';
    protected string $viewPrefix = 'laravel-admin::forms';
    protected array $validationRules;

    public function __construct()
    {
        $this->validationRules = Form::validationRules();
    }

    /**
     * Display a listing of the forms.
     */
    public function index(Request $request, $slug)
    {
        if ($slug === 'forms') {
            $forms = Form::withCount('fields')->paginate(25);
            return view($this->viewPrefix . '.index', compact('forms'));
        }
        return parent::index($request, $slug);
    }

    /**
     * Show the form for creating a new form.
     */
    public function create($slug)
    {
        if ($slug === 'forms') {
            $fieldTypes = config('laravel-admin.form_field_types', [
                'text' => 'Text Input',
                'textarea' => 'Text Area',
                'select' => 'Select Dropdown',
                'checkbox' => 'Checkbox',
                'radio' => 'Radio Buttons',
                'email' => 'Email Input',
                'number' => 'Number Input',
                'date' => 'Date Input',
                'file' => 'File Upload',
            ]);

            return view($this->viewPrefix . '.create', compact('fieldTypes'));
        }
        return parent::create($slug);
    }

    /**
     * Store a newly created form in storage.
     */
    public function store(Request $request, $slug)
    {
        if ($slug === 'forms') {
            $data = $request->validate($this->validationRules);
            $form = Form::create($data);

            // Handle fields
            if ($request->has('fields')) {
                foreach ($request->input('fields') as $order => $field) {
                    $field['order'] = $order;
                    $form->fields()->create($field);
                }
            }

            return redirect()->route('admin.forms.edit', ['slug' => 'forms', 'id' => $form->id])
                ->with('success', 'Form created successfully.');
        }
        return parent::store($request, $slug);
    }

    /**
     * Show the form for editing the specified form.
     */
    public function edit($slug, $id)
    {
        if ($slug === 'forms') {
            $form = Form::with('fields')->findOrFail($id);
            $fieldTypes = config('laravel-admin.form_field_types', [
                'text' => 'Text Input',
                'textarea' => 'Text Area',
                'select' => 'Select Dropdown',
                'checkbox' => 'Checkbox',
                'radio' => 'Radio Buttons',
                'email' => 'Email Input',
                'number' => 'Number Input',
                'date' => 'Date Input',
                'file' => 'File Upload',
            ]);

            return view($this->viewPrefix . '.edit', compact('form', 'fieldTypes'));
        }
        return parent::edit($slug, $id);
    }

    /**
     * Update the specified form in storage.
     */
    public function update(Request $request, $slug, $id)
    {
        if ($slug === 'forms') {
            $form = Form::findOrFail($id);
            $data = $request->validate(Form::updateValidationRules($id));
            
            $form->update($data);

            // Handle fields
            if ($request->has('fields')) {
                // Delete removed fields
                $existingFieldIds = collect($request->input('fields'))->pluck('id')->filter();
                $form->fields()->whereNotIn('id', $existingFieldIds)->delete();

                // Update or create fields
                foreach ($request->input('fields') as $order => $fieldData) {
                    if (!empty($fieldData['id'])) {
                        $field = FormField::find($fieldData['id']);
                        if ($field) {
                            $fieldData['order'] = $order;
                            $field->update($fieldData);
                        }
                    } else {
                        $fieldData['order'] = $order;
                        $form->fields()->create($fieldData);
                    }
                }
            } else {
                // If no fields provided, remove all existing fields
                $form->fields()->delete();
            }

            return redirect()->route('admin.forms.edit', ['slug' => 'forms', 'id' => $form->id])
                ->with('success', 'Form updated successfully.');
        }
        return parent::update($request, $slug, $id);
    }

    /**
     * Remove the specified form from storage.
     */
    public function destroy($slug, $id)
    {
        if ($slug === 'forms') {
            $form = Form::findOrFail($id);
            $form->fields()->delete();
            $form->delete();

            return redirect()->route('admin.forms.index', ['slug' => 'forms'])
                ->with('success', 'Form deleted successfully.');
        }
        return parent::destroy($slug, $id);
    }

    /**
     * Return a template for a new field.
     */
    public function fieldTemplate(Request $request)
    {
        $index = $request->query('index', 0);
        $fieldTypes = config('laravel-admin.form_field_types', [
            'text' => 'Text Input',
            'textarea' => 'Text Area',
            'select' => 'Select Dropdown',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Buttons',
            'email' => 'Email Input',
            'number' => 'Number Input',
            'date' => 'Date Input',
            'file' => 'File Upload',
        ]);

        return view('laravel-admin::forms.field', [
            'field' => [],
            'index' => $index,
            'fieldTypes' => $fieldTypes,
        ]);
    }
} 