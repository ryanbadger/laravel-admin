@extends('laravel-admin::layouts.base')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        @foreach($modelData as $slug => $data)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">{{ $data['name'] }}</h5>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $data['count'] }} {{ $data['name'] }}</h5>
                        <p class="card-text">Manage your site's {{ strtolower($data['name']) }}.</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.index', $slug) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>View All
                            </a>
                            <a href="{{ route('admin.create', $slug) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create New
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
