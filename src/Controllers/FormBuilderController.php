<?php

namespace RyanBadger\LaravelAdmin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RyanBadger\LaravelAdmin\Models\Form;
use RyanBadger\LaravelAdmin\Models\FormField;

class FormBuilderController extends Controller
{
    protected string $viewPrefix = 'laravel-admin::forms';

    /**
     * Display a listing of the forms.
     */
    public function index()
    {
        $forms = Form::withCount('fields')->paginate(25);
        return view($this->viewPrefix . '.index', compact('forms'));
    }

    /**
     * Show the form for creating a new form.
     */
    public function create()
    {
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

    /**
     * Store a newly created form in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(Form::validationRules());
        $form = Form::create($data);

        if ($request->has('fields')) {
            foreach ($request->input('fields') as $order => $field) {
                $field['order'] = $order;
                $form->fields()->create($field);
            }
        }

        return redirect()->route('admin.forms.edit', $form->id)
            ->with('success', 'Form created successfully.');
    }

    /**
     * Show the form for editing the specified form.
     */
    public function edit($id)
    {
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

    /**
     * Update the specified form in storage.
     */
    public function update(Request $request, $id)
    {
        $form = Form::findOrFail($id);
        $data = $request->validate(Form::updateValidationRules($id));
        
        $form->update($data);

        if ($request->has('fields')) {
            $existingFieldIds = collect($request->input('fields'))->pluck('id')->filter();
            $form->fields()->whereNotIn('id', $existingFieldIds)->delete();

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
            $form->fields()->delete();
        }

        return redirect()->route('admin.forms.edit', $form->id)
            ->with('success', 'Form updated successfully.');
    }

    /**
     * Remove the specified form from storage.
     */
    public function destroy($id)
    {
        $form = Form::findOrFail($id);
        $form->fields()->delete();
        $form->delete();

        return redirect()->route('admin.forms.index')
            ->with('success', 'Form deleted successfully.');
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