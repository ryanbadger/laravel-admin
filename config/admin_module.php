<?php

// Default configuration for the CMS package
return [
    // Example model configuration (this will be generated & populated by your own models automatically)
    'models' => [
        'page' => [
            'class' => \App\Models\Page::class,
            'fields' => [
                'title' => 'string',
                'slug' => 'string',
                'content' => 'text',
                'show_in_nav' => 'boolean',
            ],
            'validation' => [
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255',
                'content' => 'required|string',
                'show_in_nav' => 'boolean',
            ],
        ],
    ],
    // Add authorized emails for CMS access
    'access_emails' => [
        // 'admin@example.com',
    ],
];
