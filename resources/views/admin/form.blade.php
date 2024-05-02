@extends('laravel-admin::layouts.base')

@section('title', 'Admin - ' . (isset($record) ? 'Edit' : 'Create') . ' ' . Str::title(str_replace('_', ' ', Str::singular($slug))))

@section('content')
<div class="my-4">
    <form method="POST" action="{{ isset($record) ? route('admin.update', [$slug, $record->id]) : route('admin.store', $slug) }}" enctype="multipart/form-data">
        @csrf
        @if(isset($record))
            @method('PUT')
        @endif

        <div class="row mb-4">
            @foreach ($fields as $field => $attributes)
            
                <div class="{{ $attributes['type'] === 'textarea' ? 'col-12' : 'col-md-6' }}">
                    @include('laravel-admin::partials.input', [
                        'type' => $attributes['type'],
                        'field' => $field,
                        'attributes' => $attributes,
                        'value' => old($field, $record->$field ?? ''),
                        'editable' => $attributes['editable'],
                        'record' => $record ?? null
                    ])
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-save me-2"></i> {{ isset($record) ? 'Update' : 'Create' }}
            </button>
            @if (isset($record) && !empty($record->slug))
                <a target="_blank" href="{{ url($record->slug) }}" class="btn btn-secondary">View Page</a>
            @endif
        </div>
    </form>

    @if(isset($record))
        <form action="{{ route('admin.destroy', [$slug, $record->id]) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger mt-2" onclick="return confirm('Are you sure you want to delete this record?')">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    @endif

    {{-- <!-- Dropzone Form for File Uploads -->
    @if (isset($record) && isset($fields['media_upload']))
        @if ($fields['media_upload']['editable'])
            <form action="{{ route('admin.upload') }}" class="dropzone mt-4" id="media-dropzone"></form>
        @endif
    @endif --}}


    
</div>
@endsection


@if (isset($record) && isset($fields['media_upload']))
    <link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet">
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        Dropzone.options.mediaDropzone = {
            url: '{{ route("admin.upload") }}',
            paramName: "file", // The name as it will be sent to the server
            maxFilesize: {{ $fields['media_upload']['max_file_size'] ?? 100 }},
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.mp4",
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'  // Directly use Blade to inject CSRF token
            },
            init: function() {
                this.on("sending", function(file, xhr, formData) {
                    formData.append('model_type', '{{ addslashes(get_class($record ?? new App\Models\DefaultModel())) }}');
                    formData.append('model_id', '{{ $record->id ?? 0 }}');
                });
                this.on("success", function(file, response) {
                    console.log('Successfully uploaded:', response.fileName);
                    if (response.success) {
                        console.log('Media ID:', response.media_id);
                    }
                });
                this.on("error", function(file, response) {
                    console.error('Upload error:', response);
                });
            }
        };
    </script>
@endif


