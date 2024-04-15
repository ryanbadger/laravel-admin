<?php

// Default configuration for the CMS package
return [
    'models' => [
        // Example model configuration
        'page' => [
            'class' => \App\Models\Page::class, // Specify the model class
            'fields' => [
                'title' => 'string',  // Define field types for form generation or validation
                'slug' => 'string',
                'content' => 'text',
                'show_in_nav' => 'boolean',
            ],
            'validation' => [
                'title' => 'required|string|max:255',  // Define Laravel validation rules
                'slug' => 'required|string|max:255',
                'content' => 'required|string',
                'show_in_nav' => 'boolean',
            ],
        ],
        // Template for additional models
        // 'post' => [
        //     'class' => \App\Models\Post::class,
        //     'fields' => [
        //         'title' => 'string',
        //         'body' => 'text',
        //     ],
        //     'validation' => [
        //         'title' => 'required|string|max:255',
        //         'body' => 'required|string',
        //     ],
        // ],
    ],
];
