{{-- This is partials/input.blade.php --}}
<div class="form-group mb-4">
    <label for="{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
    @if ($type === 'text' && $editable)
        <textarea id="{{ $field }}" name="{{ $field }}" class="form-control">{{ old($field, $value) }}</textarea>
         <!-- Initialize CKEditor on textareas -->
         <script>
            ClassicEditor
                .create( document.querySelector( '#' + @json($field) ) )
                .catch( error => {
                    console.error( error );
                } );
        </script>
    @elseif ($type === 'boolean')
    <div class="form-check form-switch">
        <!-- Hidden input to ensure a value is sent when the checkbox is unchecked -->
        <input type="hidden" name="{{ $field }}" value="0">
        <input type="checkbox" 
               id="{{ $field }}" 
               name="{{ $field }}" 
               value="1" 
               class="form-check-input" 
               data-toggle="toggle" 
               {{ old($field, $value) ? 'checked' : '' }} 
               {{ !$editable ? 'disabled' : '' }}>
        <label class="form-check-label" for="{{ $field }}"></label>
    </div>
        
    @elseif ($type === 'integer' || $type === 'datetime' || $type === 'string')
        <input type="{{ $type === 'integer' ? 'number' : ($type === 'datetime' ? 'datetime-local' : 'text') }}"
               id="{{ $field }}" name="{{ $field }}"
               value="{{ old($field, $value) }}"
               class="form-control"
               {{ !$editable ? 'readonly' : '' }}>
    @else
        <input type="text" id="{{ $field }}" name="{{ $field }}"
               value="{{ old($field, $value) }}"
               class="form-control"
               {{ !$editable ? 'readonly' : '' }}>
    @endif
</div>



