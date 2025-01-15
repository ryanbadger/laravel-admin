<div class="field-container">
    @if(isset($field['id']))
        <input type="hidden" name="fields[{{ $index }}][id]" value="{{ $field['id'] }}">
    @endif

    <div class="position-absolute top-0 end-0 p-3">
        <button type="button" class="btn btn-link text-muted drag-handle me-2">
            <i class="fas fa-grip-vertical"></i>
        </button>
        <button type="button" onclick="removeField(this)" class="btn btn-link text-danger">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Field Name</label>
            <input type="text" name="fields[{{ $index }}][name]" value="{{ $field['name'] ?? '' }}" 
                class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Label</label>
            <input type="text" name="fields[{{ $index }}][label]" value="{{ $field['label'] ?? '' }}" 
                class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Type</label>
            <select name="fields[{{ $index }}][type]" 
                class="form-select"
                onchange="toggleFieldOptions(this)" required>
                @foreach($fieldTypes as $value => $label)
                    <option value="{{ $value }}" {{ ($field['type'] ?? '') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Placeholder</label>
            <input type="text" name="fields[{{ $index }}][placeholder]" value="{{ $field['placeholder'] ?? '' }}" 
                class="form-control">
        </div>

        <div class="col-12 field-options {{ in_array($field['type'] ?? '', ['select', 'radio', 'checkbox']) ? '' : 'd-none' }}">
            <label class="form-label">Options</label>
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-2">
                    <thead class="sticky-top bg-white">
                        <tr>
                            <th>Option</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="options-table">
                        @if(isset($field['options']))
                            @php
                                $options = is_array($field['options']) ? $field['options'] : (is_string($field['options']) ? json_decode($field['options'], true) : []);
                                $options = $options ?: [];
                            @endphp
                            @foreach($options as $option)
                                <tr>
                                    <td>
                                        <input type="text" class="form-control form-control-sm option-value" 
                                            value="{{ $option }}" 
                                            onchange="updateFieldOptions(this.closest('.field-options'))">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteFieldOption(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-2">
                <button type="button" class="btn btn-sm btn-primary" onclick="addFieldOption(this)">
                    <i class="fas fa-plus"></i> Add Option
                </button>
            </div>
            <input type="hidden" name="fields[{{ $index }}][options]" class="field-options-input" value="{{ isset($field['options']) ? (is_array($field['options']) ? json_encode($field['options']) : $field['options']) : '[]' }}">
        </div>

        <div class="col-md-6 field-rows {{ ($field['type'] ?? '') == 'textarea' ? '' : 'd-none' }}">
            <label class="form-label">Number of Rows</label>
            <input type="number" name="fields[{{ $index }}][rows]" value="{{ $field['rows'] ?? 3 }}" min="1" 
                class="form-control">
        </div>

        <div class="col-12">
            <div class="form-check">
                <input type="hidden" name="fields[{{ $index }}][required]" value="0">
                <input type="checkbox" name="fields[{{ $index }}][required]" value="1" 
                    {{ ($field['required'] ?? false) ? 'checked' : '' }}
                    class="form-check-input" id="required_{{ $index }}">
                <label class="form-check-label" for="required_{{ $index }}">Required</label>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleFieldOptions(select) {
        const container = select.closest('.field-container');
        const optionsDiv = container.querySelector('.field-options');
        const rowsDiv = container.querySelector('.field-rows');
        
        if (['select', 'radio', 'checkbox'].includes(select.value)) {
            optionsDiv.classList.remove('d-none');
            updateFieldOptions(optionsDiv);
        } else {
            optionsDiv.classList.add('d-none');
            container.querySelector('.field-options-input').value = '[]';
        }

        if (select.value === 'textarea') {
            rowsDiv.classList.remove('d-none');
        } else {
            rowsDiv.classList.add('d-none');
        }
    }

    function addFieldOption(button) {
        const container = button.closest('.field-options');
        const tbody = container.querySelector('.options-table');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" class="form-control form-control-sm option-value" 
                    onchange="updateFieldOptions(this.closest('.field-options'))">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="deleteFieldOption(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        updateFieldOptions(container);
    }

    function deleteFieldOption(button) {
        const container = button.closest('.field-options');
        button.closest('tr').remove();
        updateFieldOptions(container);
    }

    function updateFieldOptions(container) {
        const options = Array.from(container.querySelectorAll('.option-value'))
            .map(input => input.value.trim())
            .filter(Boolean);
        container.querySelector('.field-options-input').value = JSON.stringify(options);
    }
</script>
@endpush

@push('styles')
<style>
    .table-responsive {
        scrollbar-width: thin;
        scrollbar-color: #dee2e6 #fff;
    }
    .table-responsive::-webkit-scrollbar {
        width: 6px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #fff;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background-color: #dee2e6;
        border-radius: 3px;
    }
    .sticky-top {
        top: 0;
        z-index: 1;
    }
</style>
@endpush 