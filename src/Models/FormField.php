<?php

namespace RyanBadger\LaravelAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    protected $fillable = [
        'form_id',
        'name',
        'label',
        'type',
        'required',
        'placeholder',
        'rows',
        'options',
        'order',
    ];

    protected $casts = [
        'required' => 'boolean',
        'options' => 'array',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the form that owns the field.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Set the options attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setOptionsAttribute($value)
    {
        if (is_string($value)) {
            // If it's already a JSON string, store it directly
            if ($this->isJson($value)) {
                $this->attributes['options'] = $value;
            } else {
                // If it's a newline-separated string, convert to array and then to JSON
                $options = explode("\n", $value);
                $options = array_map('trim', $options);
                $options = array_filter($options);
                $this->attributes['options'] = json_encode(array_values($options));
            }
        } else {
            // If it's already an array or null, encode it
            $this->attributes['options'] = json_encode($value);
        }
    }

    /**
     * Check if a string is valid JSON.
     *
     * @param string $string
     * @return bool
     */
    private function isJson($string) {
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * Get the validation rules for this model.
     */
    public static function validationRules(): array
    {
        return [
            'form_id' => 'required|exists:forms,id',
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
            'rows' => 'nullable|integer|min:1',
            'options' => 'nullable|array',
            'order' => 'integer|min:0',
        ];
    }
} 