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

            <div class="row mb-4">
                @foreach ($modelConfig['fields'] as $field => $attributes)
                    <div class="{{ $attributes['type'] === 'text' ? 'col-12' : 'col-md-6' }}">
                        @include('laravel-admin::partials.input', [
                            'type' => $attributes['type'],
                            'field' => $field,
                            'value' => old($field, $record->$field ?? ''),
                            'editable' => $attributes['editable']
                        ])
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($record) ? 'Update' : 'Create' }}</button>

            <!-- Optionally View Page Button if a specific method exists -->
            @if (isset($record) && !empty($record->slug))
                <a target="_blank" href="{{ url($record->slug) }}" class="btn btn-secondary">View Page</a>
            @endif

        </form>
    </div>
@endsection
