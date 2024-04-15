@extends('laravel-admin::layouts.base')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="row">
        @foreach($modelData as $model => $data)
            <div class="col-lg-4 col-md-6">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">{{ $data['name'] }}</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $data['count'] }} {{ $data['name'] }}</h5>
                        <p class="card-text">Manage your site's {{ strtolower($data['name']) }}.</p>
                        <a href="{{ route('admin.index', $model) }}" class="btn btn-secondary">View {{ $data['name'] }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- Additional dashboard elements can be added here -->
@endsection
