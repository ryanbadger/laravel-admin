<?php

namespace RyanBadger\LaravelAdmin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\FormField;

class FormFieldController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_id' => 'required|exists:forms,id',
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:text,textarea,select,checkbox,email,tel',
            'required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
            'rows' => 'nullable|integer|min:1',
            'options' => 'nullable|json',
            'order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        
        // Handle existing field update
        if ($request->filled('field_id')) {
            $field = FormField::findOrFail($request->field_id);
            $field->update($data);
        } else {
            // Create new field
            $field = FormField::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Field saved successfully',
            'field' => $field
        ]);
    }

    public function destroy($id)
    {
        $field = FormField::findOrFail($id);
        $field->delete();

        return response()->json([
            'success' => true,
            'message' => 'Field deleted successfully'
        ]);
    }

    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fields' => 'required|array',
            'fields.*' => 'required|exists:form_fields,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        foreach ($request->fields as $index => $fieldId) {
            FormField::where('id', $fieldId)->update(['order' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Field order updated successfully'
        ]);
    }
} 