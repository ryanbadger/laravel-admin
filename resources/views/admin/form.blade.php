@extends('laravel-admin::layouts.base')

@section('title', isset($record) ? 'Edit ' . Str::title(str_replace('_', ' ', Str::singular($slug))) : 'Create ' . Str::title(str_replace('_', ' ', Str::singular($slug))))

@section('header_buttons')
    <a href="{{ route('admin.index', $slug) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to {{ ucfirst($slug) }}
    </a>
@endsection

@section('content')
    <form id="mainForm" method="POST" action="{{ isset($record) ? route('admin.update', [$slug, $record->id]) : route('admin.store', $slug) }}" enctype="multipart/form-data">
        @csrf
        @if(isset($record))
            @method('PUT')
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    @foreach ($fields as $field => $attributes)
                        <div class="{{ $attributes['type'] === 'textarea' || $attributes['type'] === 'media' ? 'col-12' : 'col-md-6' }} mb-3">
                            @include('laravel-admin::partials.input', [
                                'type' => $attributes['type'],
                                'field' => $field,
                                'attributes' => $attributes,
                                'value' => $fieldValues[$field] ?? null,
                                'editable' => $attributes['editable'],
                                'record' => $record ?? null
                            ])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>{{ isset($record) ? 'Update' : 'Create' }}
                </button>
                @if (isset($record) && !empty($record->slug))
                    <a target="_blank" href="{{ url($record->slug) }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-external-link-alt me-2"></i>View Page
                    </a>
                @endif
            </div>
        </div>
    </form>

    @if(isset($record))
        <form id="deleteForm" action="{{ route('admin.destroy', [$slug, $record->id]) }}" method="POST" class="mt-3">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                <i class="fas fa-trash me-2"></i>Delete
            </button>
        </form>
    @endif
@endsection

@if (isset($record) && isset($fields['media_upload']))
    @push('styles')
        <link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet">
    @endpush
@endif
