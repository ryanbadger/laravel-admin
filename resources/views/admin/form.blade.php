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
                <div class="{{ $attributes['type'] === 'textarea' || $attributes['type'] === 'media' ? 'col-12' : 'col-md-6' }}">
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

        <!-- Display attached media -->
        @if (isset($record) && isset($fields['media_upload']))
            <div class="row" id="media-preview">
                @foreach ($record->media as $media)
                    <div class="col-sm-4 col-md-3 col-lg-2 mb-4 d-flex">
                        <div class="card w-100 p-2">
                            <div class="media-container" style="height: 150px;">
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    @if ($media->isImage())
                                        <img src="{{ $media->getUrl() }}" alt="Preview" class="img-fluid object-fit-cover rounded w-100 h-100">
                                    @else
                                        <video class="mh-100 mw-100 m-auto d-block" controls>
                                            <source src="{{ $media->getUrl() }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center text-center">
                                <h6 class="card-title d-block text-truncate" title="{{ $media->file_name }}">
                                    {{ $media->file_name }}
                                </h6>
                                <button type="button" class="btn btn-danger btn-sm mt-2" data-media-id="{{ $media->id }}" onclick="deleteMedia({{ $media->id }})">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

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
</div>
@endsection

@if (isset($record) && isset($fields['media_upload']))
    <link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet">
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        Dropzone.options.mediaDropzone = {
            url: '{{ route("admin.upload") }}',
            paramName: "file",
            maxFilesize: {{ $fields['media_upload']['max_file_size'] ?? 100 }},
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.mp4",
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                        // Append the newly uploaded media to the media preview container
                        var mediaPreview = document.getElementById('media-preview');
                        var mediaHtml = `
                            <div class="col-sm-4 col-md-3 col-lg-2 mb-4 d-flex">
                                <div class="card w-100 p-2">
                                    <div class="media-container" style="height: 150px;">
                                        <a href="${response.url}" target="_blank">
                                            <img src="${response.url}" alt="Preview" class="img-fluid object-fit-cover rounded w-100 h-100">
                                        </a>
                                    </div>
                                    <div class="card-body d-flex flex-column justify-content-center text-center">
                                        <h6 class="card-title d-block text-truncate" title="${response.fileName}">${response.fileName}</h6>
                                        <button type="button" class="btn btn-danger btn-sm mt-2" data-media-id="${response.media_id}" onclick="deleteMedia(${response.media_id})">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        mediaPreview.insertAdjacentHTML('beforeend', mediaHtml);
                    }
                });
                this.on("error", function(file, response) {
                    console.error('Upload error:', response);
                });
            }
        };

        function deleteMedia(mediaId) {
            if (confirm('Are you sure you want to delete this media item?')) {
                // Send an AJAX request to delete the media item
                var xhr = new XMLHttpRequest();
                xhr.open('DELETE', '{{ route("admin.media.destroy", ":id") }}'.replace(':id', mediaId), true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Remove the deleted media item from the DOM
                        var mediaElement = document.querySelector('[data-media-id="' + mediaId + '"]').closest('.col-sm-4');
                        mediaElement.remove();
                    } else {
                        console.error('Error deleting media item:', xhr.responseText);
                    }
                };
                xhr.send();
            }
        }
    </script>
@endif