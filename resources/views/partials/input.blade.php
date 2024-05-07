<div class="form-group mb-4">

    <label for="{{ $field }}">{{ $attributes['label'] }}</label>

    @switch($type)
        @case('textarea')
            @if($editable)
                <textarea id="{{ $field }}" name="{{ $field }}" class="form-control">{{ old($field, $value) }}</textarea>
                <script>
                    ClassicEditor
                        .create(document.querySelector('#{{ $field }}'), {
                            ckfinder: {
                                uploadUrl: '/admin/upload?_token={{ csrf_token() }}'
                            },
                            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo', 'sourceEditing' ],
                            // Disable content filtering
                            htmlSupport: {
                                allow: [
                                    {
                                        name: /.*/,
                                        attributes: true,
                                        classes: true,
                                        styles: true
                                    }
                                ]
                            }

                        })
                        .catch(error => {
                            console.error(error);
                        });
                </script>
            @endif
            @break

        @case('checkbox')
            <div class="form-check form-switch">
                <input type="hidden" name="{{ $field }}" value="0">
                <input type="checkbox" id="{{ $field }}" name="{{ $field }}" value="1" class="form-check-input" {{ old($field, $value) ? 'checked' : '' }} {{ !$editable ? 'disabled' : '' }}>
                <label class="form-check-label" for="{{ $field }}"></label>
            </div>
            @break

        @case('number')
        @case('text')
        @case('date')
        @case('datetime-local')
        @case('string')
            <input type="{{ $type }}" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $value) }}" class="form-control" {{ !$editable ? 'readonly' : '' }}>
            @break

        @case('media')
            @if ($attributes['editable'])
                <div class="dropzone" id="media-dropzone"></div>
            @else
                @if (isset($record))
                    @if ($record->isImage())
                        <img src="{{ $record->getUrl() }}" alt="Image Preview" class="d-block" style="max-width: 128px; height: auto;">
                    @else
                        {{-- render video here --}}
                    <video width="100%" height="400" controls class="d-block">
                        <source src="{{ $record->getUrl() }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    @endif
                @endif
            @endif
            @break
        
        
        @case('select')
            <select id="{{ $field }}" name="{{ $field }}" class="form-control" {{ !$editable ? 'disabled' : '' }}>
                @if (!$attributes['required']) <!-- Check if the field is not required -->
                    <option value="">Please select</option> <!-- Add the placeholder option -->
                @endif
                @foreach ($attributes['options'] as $optionKey => $optionValue)
                    <option value="{{ $optionKey }}" {{ (old($field, $value) == $optionKey) ? 'selected' : '' }}>
                        {{ $optionValue }}
                    </option>
                @endforeach
            </select>
            @break

        @default
            <input type="text" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $value) }}" class="form-control" {{ !$editable ? 'readonly' : '' }}>
            @break
    @endswitch
</div>
