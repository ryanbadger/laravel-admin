@extends('laravel-admin::layouts.base')

@section('title', 'Admin - ' . (isset($record) ? 'Edit' : 'Create') . ' ' . Str::title(str_replace('_', ' ', Str::singular($slug))))

@section('content')
    <div class="my-4">
        <h1>{{ isset($record) ? 'Edit' : 'Create' }} {{ Str::title(str_replace('_', ' ', Str::singular($slug))) }}</h1>
        <form method="POST" action="{{ isset($record) ? route('admin.update', [$slug, $record->id]) : route('admin.store', $slug) }}" class="mt-4">
            @csrf
            @if (isset($record))
                @method('PUT')
            @endif

            @foreach ($fields as $field => $type)
                @include('laravel-admin::partials.input', ['type' => $type, 'field' => $field, 'value' => old($field, $record->$field ?? '')])
            @endforeach

            <button type="submit" class="btn btn-primary">{{ isset($record) ? 'Update' : 'Create' }}</button>

            <!-- View Page Button -->
            @if (isset($record))
                <a target="_blank" href="{{ "/".$record["slug"] }}" class="btn btn-secondary">View Page</a>
            @endif

        </form>
    </div>
@endsection
