<div class="mb-3">
    @php
        $inputType = 'text'; // Default input type
        switch ($type) {
            case 'bigint':
            case 'integer':
                $inputType = 'number';
                break;
            case 'boolean':
            case 'tinyint': // Treat 'tinyint' as boolean if applicable
                $inputType = 'checkbox';
                break;
            case 'text':
                $inputType = 'textarea';
                break;
            case 'timestamp':
                $inputType = 'datetime-local';
                break;
        }
    @endphp

    <label for="{{ $field }}" class="form-label">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>

    @if($inputType == 'textarea')
        <textarea class="form-control" name="{{ $field }}" id="{{ $field }}">{{ old($field, $value) }}</textarea>
        
        <!-- Initialize CKEditor on textareas -->
        <script>
            ClassicEditor
                .create( document.querySelector( '#' + @json($field) ) )
                .catch( error => {
                    console.error( error );
                } );
        </script>

        
    @elseif($inputType == 'checkbox')
        <div class="form-check form-switch">
            <!-- Hidden input to ensure a value is sent when the checkbox is unchecked -->
            <input type="hidden" name="{{ $field }}" value="0">
            <input class="form-check-input" type="checkbox" role="switch" name="{{ $field }}" id="{{ $field }}" value="1" {{ old($field, $value) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $field }}"></label>
        </div>
    @else
        <input type="{{ $inputType }}" class="form-control" name="{{ $field }}" id="{{ $field }}" value="{{ old($field, $value) }}">
    @endif
</div>
