@extends('laravel-admin::layouts.base')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="row">
        @foreach($modelData as $slug => $data)
            <div class="col-lg-4 col-md-6">
                <div class="card mb-3">
                    <div class="card-header">{{ $data['name'] }}</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $data['count'] }} {{ $data['name'] }}</h5>
                        <p class="card-text">Manage your site's {{ strtolower($data['name']) }}.</p>
                        <a href="{{ route('admin.index', $slug) }}" class="btn btn-secondary">View</a>
                        <a href="{{ route('admin.create', $slug) }}" class="btn btn-primary">Create New {{ ucfirst(Str::singular($slug)) }}</a>


                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- Additional dashboard elements can be added here -->
@endsection
