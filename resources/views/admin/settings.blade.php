@extends('laravel-admin::layouts.base')

@section('title', 'System Information')

@section('content')
    <div class="my-4">
        <div class="row">
            <!-- Site Configuration -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-globe me-2"></i>Site Configuration</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th>App Name</th>
                                    <td>{{ config('app.name', 'Not Set') }}</td>
                                </tr>
                                <tr>
                                    <th>App URL</th>
                                    <td>{{ config('app.url', 'Not Set') }}</td>
                                </tr>
                                <tr>
                                    <th>Timezone</th>
                                    <td>{{ config('app.timezone', 'UTC') }}</td>
                                </tr>
                                <tr>
                                    <th>Default Locale</th>
                                    <td>{{ config('app.locale', 'en') }}</td>
                                </tr>
                                <tr>
                                    <th>Mail From Name</th>
                                    <td>{{ config('mail.from.name', 'Not Set') }}</td>
                                </tr>
                                <tr>
                                    <th>Mail From Address</th>
                                    <td>{{ config('mail.from.address', 'Not Set') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-server me-2"></i>System Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th>PHP Version</th>
                                    <td>{{ phpversion() }}</td>
                                </tr>
                                <tr>
                                    <th>Laravel Version</th>
                                    <td>{{ app()->version() }}</td>
                                </tr>
                                <tr>
                                    <th>Admin Package Version</th>
                                    <td>v{{ $laravel_admin_version }}</td>
                                </tr>
                                <tr>
                                    <th>Server OS</th>
                                    <td>{{ php_uname('s') . ' ' . php_uname('r') }}</td>
                                </tr>
                                <tr>
                                    <th>Memory Limit</th>
                                    <td>{{ ini_get('memory_limit') }}</td>
                                </tr>
                                <tr>
                                    <th>Max Upload Size</th>
                                    <td>{{ ini_get('upload_max_filesize') }}</td>
                                </tr>
                                <tr>
                                    <th>Max Post Size</th>
                                    <td>{{ ini_get('post_max_size') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Application Status -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Application Status</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th>Environment</th>
                                    <td>{{ app()->environment() }}</td>
                                </tr>
                                <tr>
                                    <th>Debug Mode</th>
                                    <td>
                                        @if(config('app.debug'))
                                            <span class="badge bg-warning">Enabled</span>
                                        @else
                                            <span class="badge bg-success">Disabled</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Cache Driver</th>
                                    <td>{{ config('cache.default', 'Not Set') }}</td>
                                </tr>
                                <tr>
                                    <th>Session Driver</th>
                                    <td>{{ config('session.driver', 'Not Set') }}</td>
                                </tr>
                                <tr>
                                    <th>Queue Connection</th>
                                    <td>{{ config('queue.default', 'Not Set') }}</td>
                                </tr>
                                <tr>
                                    <th>Mail Driver</th>
                                    <td>{{ config('mail.default', 'Not Set') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Package Information -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-box me-2"></i>Package Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th>Storage Driver</th>
                                    <td>{{ config('filesystems.default', 'Not Set') }}</td>
                                </tr>
                                <tr>
                                    <th>Pagination Size</th>
                                    <td>{{ $pagination_size ?? 25 }} items</td>
                                </tr>
                                <tr>
                                    <th>Registered Models</th>
                                    <td>{{ $model_count ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Assets Published</th>
                                    <td>
                                        @if(file_exists(public_path('vendor/laravel-admin')))
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-warning">No</span>
                                            <small class="text-muted d-block">Run: php artisan vendor:publish --tag=laravel-admin-assets</small>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PHP Extensions -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-puzzle-piece me-2"></i>Required Extensions</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                @php
                                    $requiredExtensions = [
                                        'fileinfo' => 'FileInfo',
                                        'gd' => 'GD Library',
                                        'json' => 'JSON',
                                        'mbstring' => 'Mbstring',
                                        'openssl' => 'OpenSSL',
                                        'pdo' => 'PDO',
                                        'tokenizer' => 'Tokenizer',
                                        'xml' => 'XML'
                                    ];
                                @endphp

                                @foreach($requiredExtensions as $ext => $name)
                                    <tr>
                                        <th>{{ $name }}</th>
                                        <td>
                                            @if(extension_loaded($ext))
                                                <span class="badge bg-success">Installed</span>
                                            @else
                                                <span class="badge bg-danger">Missing</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 