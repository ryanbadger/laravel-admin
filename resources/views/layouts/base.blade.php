<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Include the CSS for the admin panel -->
    <link href="{{ asset('vendor/laravel-admin/css/admin.css') }}" rel="stylesheet">
    <!-- Include your custom Bootstrap CSS -->
    <link href="{{ asset('vendor/laravel-admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('vendor/laravel-admin/css/all.min.css') }}" rel="stylesheet">
    <!-- Include the CKEditor script -->
    <script src="{{ asset('vendor/laravel-admin/js/ckeditor.js') }}"></script>
    <!-- Include your custom Bootstrap JavaScript -->
    <script src="{{ asset('vendor/laravel-admin/js/bootstrap.min.js') }}"></script>


</head>
<body>
    <header class="navbar navbar-dark bg-dark sticky-top flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </header>
    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse custom-sidebar d-flex flex-column justify-content-between">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.index', 'page') }}">
                                <i class="fas fa-file-alt"></i> Pages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.index', 'media') }}">
                                <i class="fas fa-image"></i> Media
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.index', 'user') }}">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.settings') }}"> <!-- Ensure there is a settings route -->
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    </ul>
                    
                </div>

                {{-- Render the package version in your view --}}
                <div>
                    <!-- "Package Version" paragraph -->
                    <p class="text-center">v{{ $laravel_admin_version }}</p>
                </div>

            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('title')</h1>
                    <!-- Placeholder for additional header buttons or links -->
                    @yield('header_buttons')
                </div>

                 <!-- Error Display Section -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Include the JavaScript for the admin panel -->
    <script type="module" src="{{ asset('vendor/laravel-admin/js/admin.js') }}"></script>
    

</body>
</html>
