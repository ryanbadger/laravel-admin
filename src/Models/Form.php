<?php

namespace RyanBadger\LaravelAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($form) {
            // Clear the cache for the form's page
            \Cache::forget('page-' . $form->slug);
        });

        static::deleted(function ($form) {
            // Clear the cache when a form is deleted
            \Cache::forget('page-' . $form->slug);
        });
    }

    /**
     * Get the fields for the form.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    /**
     * Get the validation rules for this model.
     */
    public static function validationRules(): array
    {
        return [
            'slug' => 'required|string|max:255|unique:forms,slug',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Get the validation rules for updating this model.
     */
    public static function updateValidationRules($id): array
    {
        return [
            'slug' => 'required|string|max:255|unique:forms,slug,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Define the fields that should be shown in the CMS.
     */
    public function cmsFields(): array
    {
        return [
            'name' => [
                'label' => 'Form Name',
                'type' => 'text',
                'required' => true,
                'editable' => true,
                'searchable' => true,
                'show_in_list' => true,
            ],
            'slug' => [
                'label' => 'Slug',
                'type' => 'text',
                'required' => true,
                'editable' => true,
                'searchable' => true,
                'show_in_list' => true,
            ],
            'description' => [
                'label' => 'Description',
                'type' => 'textarea',
                'required' => false,
                'editable' => true,
                'searchable' => true,
                'show_in_list' => true,
            ],
            'fields_count' => [
                'label' => 'Fields',
                'type' => 'text',
                'required' => false,
                'editable' => false,
                'searchable' => false,
                'show_in_list' => true,
                'value' => function ($model) {
                    return $model->fields()->count();
                },
            ],
        ];
    }
} 