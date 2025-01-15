<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Include the CSS for the admin panel -->
    <link href="{{ asset('vendor/laravel-admin/css/admin.css') }}" rel="stylesheet">
    <!-- Include Bootstrap CSS -->
    <link href="{{ asset('vendor/laravel-admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('vendor/laravel-admin/css/all.min.css') }}" rel="stylesheet">
    <!-- Include the CKEditor script -->
    <script src="{{ asset('vendor/laravel-admin/js/ckeditor.js') }}"></script>
    <!-- Include Bootstrap JavaScript -->
    <script src="{{ asset('vendor/laravel-admin/js/bootstrap.min.js') }}"></script>
    <!-- Include Sortable.js -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>

    <style>
        @media (max-width: 767.98px) {
            #sidebarMenu {
                transition: transform .05s ease-in-out !important;
            }
            #sidebarMenu.collapsing {
                transition: transform .05s ease-in-out !important;
            }
        }
    </style>

    <!-- Additional styles -->
    @stack('styles')
</head>
<body class="h-100">
    <header class="navbar navbar-dark bg-dark fixed-top flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">
            Admin Panel v{{ $laravel_admin_version }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light position-fixed start-0 h-100 pt-5 collapse" style="z-index: 1021;">
                <div class="pt-3 overflow-auto h-100">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-home fa-fw me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="{{ route('admin.index', 'page') }}">
                                <i class="fas fa-file-alt fa-fw me-2"></i>Pages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="{{ route('admin.index', 'media') }}">
                                <i class="fas fa-image fa-fw me-2"></i>Media
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="{{ route('admin.forms.index') }}">
                                <i class="fas fa-poll-h fa-fw me-2"></i>Forms
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="{{ route('admin.index', 'user') }}">
                                <i class="fas fa-users fa-fw me-2"></i>Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="{{ route('admin.settings') }}">
                                <i class="fas fa-cog fa-fw me-2"></i>Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a class="nav-link d-flex align-items-center" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt fa-fw me-2"></i>Sign out
                            </a> 
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4">
                        <h1 class="h2">@yield('title')</h1>
                        @hasSection('header_buttons')
                            @yield('header_buttons')
                        @endif
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Include the JavaScript for the admin panel -->
    <script type="module" src="{{ asset('vendor/laravel-admin/js/admin.js') }}"></script>
    
    <!-- Additional scripts -->
    @stack('scripts')
</body>
</html>
