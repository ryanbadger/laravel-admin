# Laravel Admin Package

This Laravel Admin package simplifies the administration of models in a Laravel application. It provides a dynamic admin panel for managing models with basic CRUD operations, tailored to handle different data types effectively.

## Features

-   Dynamic model detection and CRUD generation.
-   Custom input types based on model attribute data types.
-   Simplified setup process, ideal for rapid development and prototyping.

## Installation

To install the package, follow these steps:

### Step 1: Install the Package

Run the following command in your Laravel project:

`composer require RyanBadger/laravel-admin`

### Step 2: Publish the Assets & Run Migration (for media support)

Publish the assets for the package:

`php artisan vendor:publish --tag=laravel-admin-assets`

Run Migration

`php artisan migrate`

This commands will publish the necessary views and assets to your Laravel project, and create the Media model for file upload support on any model.

### Step 3: Configure Your Models

Ensure that your models are set up correctly with the `$fillable` property to allow mass assignment, and define the CMS fields.

```
class YourModel extends Model { 
    protected $fillable = [
        'field1', 
        'field2', 
        'field3'
    ]; 
}
```

```
public function cmsFields() {  
    return [
            'title' => [
                'type' => 'text', // Field type (text, textarea, select, checkbox, media, etc.)
                'label' => 'Title', // Human-readable field name
                'editable' => true, // Allow this field to be edited in the CMS
                'required' => true, // Require this field to be filled out
                'show_in_list' => true, // Show this field in the CMS list view
                'searchable' => true, // Make this field searchable
            ],
            'template' => [
                'type' => 'select', // Show a select dropdown with the options you define
                'label' => 'Page Template',
                'options' => [
                    'page' => 'Default Page',
                    'videos' => 'Video Page'
                ],
                'editable' => true,
                'required' => true,
                'show_in_list' => true,
            ],
            'show_in_nav' => [
                'type' => 'checkbox', // Displays a bootstrap toggle
                'label' => 'Show in Navigation',
                'editable' => true,
                'required' => true,
            ],
            'body' => [
                'type' => 'textarea', // Displays a CKEditor WYSIWYG
                'label' => 'Body',
                'editable' => true,
                'required' => false,
                'searchable' => true,
            ],
            'media_upload' => [
                'type' => 'media', // Displays a Dropzone.js uploader
                'label' => 'Media Upload',
                'multiple' => true,
                'max_files' => 99,
                'max_file_size' => 99, // in MB
                'allowed_types' => 'image/jpeg,image/png,image/gif',
                'editable' => true,
                'required' => false,
            ],
        ];
```



## Usage

Once installed, the last step is to grant access to your admin users.

This package check your "users" table/model for an is_admin value. You should create this yourself, or if you prefer a different method, update the CMS middleware.

Once logged in, navigate to `/admin/` in your web browser to manage your app.

### Dashboard

Access the dashboard at:

`/admin/dashboard`

This dashboard shows a summary of all models and their basic stats.

## Contributing

Contributions are welcome. Please open an issue or submit a pull request with your improvements.

## License

This Laravel Admin package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
