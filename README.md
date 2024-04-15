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

### Step 2: Publish the Assets

Publish the assets and configurations of the package:

`php artisan vendor:publish --tag=laravel-admin-assets`
`php artisan vendor:publish --provider="RyanBadger\LaravelAdmin\AdminModuleServiceProvider"`

This command will publish the necessary views, config, and assets to your Laravel project.

### Step 3: Configure Your Models

Ensure that your models are set up correctly with the `$fillable` property to allow mass assignment:


`class YourModel extends Model { 
    protected $fillable = ['field1', 'field2', 'field3']; // etc. 
}`

## Usage

Once installed, navigate to `/admin/{model}` in your web browser to manage your models. `{model}` should be replaced with the actual model name in snake_case.

### Dashboard

Access the dashboard at:

`/admin/dashboard`

This dashboard shows a summary of all models and their basic stats.

## Customization

You can extend the functionality by modifying the published config file in the `config/admin_module.php`.

## Contributing

Contributions are welcome. Please open an issue or submit a pull request with your improvements.

## License

This Laravel Admin package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
