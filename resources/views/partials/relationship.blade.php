@php
    $relationship = $attributes['relationship'] ?? null;
    $relationshipType = $relationship['type'] ?? 'belongsTo';
    $orderColumn = $relationship['order_column'] ?? null;
    $displayColumn = $relationship['display_column'] ?? 'name';
    
    // Get currently selected values with eager loading
    $fields = isset($model) ? $model->fields()->orderBy('order')->get() : collect();
    
    // Convert fields to JSON for JavaScript
    $fieldsJson = $fields->map(function($field) {
        // Ensure options is an array
        $options = $field->options;
        if (is_string($options)) {
            $options = json_decode($options, true) ?? [];
        } elseif (!is_array($options)) {
            $options = [];
        }
        
        return [
            'id' => (string)$field->id,
            'name' => $field->name,
            'label' => $field->label,
            'type' => $field->type,
            'required' => (bool)$field->required,
            'placeholder' => $field->placeholder,
            'rows' => $field->rows,
            'options' => $options,
            'order' => (int)$field->order,
        ];
    })->keyBy(function($field) {
        return (string)$field['id'];
    })->toJson(JSON_PRETTY_PRINT);
@endphp

<div class="form-group">
    <label for="{{ $field }}">{{ $attributes['label'] }}</label>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Form Fields</h6>
            <button type="button" class="btn btn-sm btn-primary" onclick="addNewField()">
                <i class="fas fa-plus"></i> Add New Field
            </button>
        </div>
        <div class="card-body">
            <div class="form-fields-builder">
                <div class="fields-list">
                    <div class="list-group" id="fields-list">
                        @foreach($fields as $field)
                            <div class="list-group-item d-flex align-items-center gap-3" draggable="true" data-field-id="{{ $field->id }}">
                                <div class="drag-handle">
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>{{ $field->label }}</strong>
                                    <div class="text-muted small">
                                        Type: {{ ucfirst($field->type) }}
                                        @if($field->required)
                                            <span class="badge bg-danger">Required</span>
                                        @endif
                                        @if($field->type === 'select' && !empty($field->options))
                                            <div class="mt-1">
                                                Options: {{ implode(', ', is_array($field->options) ? $field->options : json_decode($field->options, true) ?? []) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary" 
                                            onclick="editField('{{ $field->id }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteField('{{ $field->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Field Editor Modal -->
<div class="modal fade" id="fieldEditorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="fieldForm" method="POST">
                    @csrf
                    <input type="hidden" name="field_id" id="field_id">
                    <input type="hidden" name="form_id" value="{{ $model->id }}">
                    <div class="mb-3">
                        <label for="field_name">Field Name</label>
                        <input type="text" class="form-control" id="field_name" name="name" required>
                        <div class="form-text">Internal field name (e.g., 'email_address')</div>
                    </div>
                    <div class="mb-3">
                        <label for="field_label">Field Label</label>
                        <input type="text" class="form-control" id="field_label" name="label" required>
                        <div class="form-text">Display label (e.g., 'Email Address')</div>
                    </div>
                    <div class="mb-3">
                        <label for="field_type">Field Type</label>
                        <select class="form-control" id="field_type" name="type" required onchange="handleFieldTypeChange()">
                            <option value="text">Text Input</option>
                            <option value="textarea">Text Area</option>
                            <option value="select">Select Dropdown</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="email">Email Input</option>
                            <option value="tel">Phone Input</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="field_required" name="required">
                            <label class="form-check-label" for="field_required">Required Field</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="field_placeholder">Placeholder Text</label>
                        <input type="text" class="form-control" id="field_placeholder" name="placeholder">
                        <div class="form-text">Helpful text shown when the field is empty</div>
                    </div>
                    <div class="mb-3" id="rows_group" style="display: none;">
                        <label for="field_rows">Number of Rows</label>
                        <input type="number" class="form-control" id="field_rows" name="rows" min="2" value="3">
                        <div class="form-text">Number of visible rows for textarea</div>
                    </div>
                    <div class="mb-3" id="options_group" style="display: none;">
                        <label>Options</label>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Option</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="options-table">
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addOption()">
                                <i class="fas fa-plus"></i> Add Option
                            </button>
                        </div>
                        <input type="hidden" name="options" id="field_options">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Field</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.form-fields-builder {
    display: grid;
    gap: 1rem;
}
.fields-list .list-group-item {
    border-left: 4px solid var(--bs-primary);
    cursor: move;
}
.drag-handle {
    cursor: move;
    padding: 0.25rem;
}
.dragging {
    opacity: 0.5;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store all field data in JavaScript
    const formFields = JSON.parse(@json($fieldsJson));
    const fieldForm = document.getElementById('fieldForm');
    const fieldEditorModal = document.getElementById('fieldEditorModal');
    
    // Initialize Bootstrap modal
    let modal;
    if (typeof bootstrap !== 'undefined') {
        modal = new bootstrap.Modal(fieldEditorModal);
    } else {
        console.error('Bootstrap JavaScript is not loaded');
        return;
    }

    // Handle form submission
    if (fieldForm) {
        fieldForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error saving field: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving field. Please try again.');
            });
        });
    }

    // Initialize drag and drop
    const fieldsList = document.getElementById('fields-list');
    let dragSrcEl = null;
    
    if (fieldsList) {
        // Make fields sortable
        fieldsList.addEventListener('dragstart', function(e) {
            dragSrcEl = e.target.closest('.list-group-item');
            dragSrcEl.classList.add('dragging');
        });
        
        fieldsList.addEventListener('dragover', function(e) {
            e.preventDefault();
            return false;
        });
        
        fieldsList.addEventListener('drop', function(e) {
            e.preventDefault();
            const target = e.target.closest('.list-group-item');
            
            if (dragSrcEl !== target) {
                const allItems = [...fieldsList.children];
                const draggedPos = allItems.indexOf(dragSrcEl);
                const droppedPos = allItems.indexOf(target);
                
                if (draggedPos < droppedPos) {
                    target.parentNode.insertBefore(dragSrcEl, target.nextSibling);
                } else {
                    target.parentNode.insertBefore(dragSrcEl, target);
                }
                
                updateFieldOrder();
            }
            
            dragSrcEl.classList.remove('dragging');
            return false;
        });
        
        fieldsList.addEventListener('dragend', function(e) {
            e.target.classList.remove('dragging');
        });
    }

    // Define all functions in the scope where we have access to our elements
    window.handleFieldTypeChange = function() {
        const fieldType = document.getElementById('field_type').value;
        document.getElementById('rows_group').style.display = fieldType === 'textarea' ? 'block' : 'none';
        document.getElementById('options_group').style.display = fieldType === 'select' ? 'block' : 'none';
    };

    window.addNewField = function() {
        if (!fieldForm || !modal) return;
        
        // Reset form fields
        fieldForm.reset();
        fieldForm.action = "{{ route('admin.form-fields.store') }}";
        
        document.getElementById('field_id').value = '';
        document.getElementById('options-table').innerHTML = '';
        document.getElementById('field_options').value = '';
        
        handleFieldTypeChange();
        modal.show();
    };

    window.editField = function(fieldId) {
        if (!fieldForm || !modal) return;
        
        const field = formFields[String(fieldId)];
        if (!field) {
            console.error('Field not found:', fieldId);
            return;
        }

        fieldForm.action = "{{ route('admin.form-fields.store') }}";

        document.getElementById('field_id').value = fieldId;
        document.getElementById('field_name').value = field.name || '';
        document.getElementById('field_label').value = field.label || '';
        document.getElementById('field_type').value = field.type || 'text';
        document.getElementById('field_required').checked = Boolean(field.required);
        document.getElementById('field_placeholder').value = field.placeholder || '';
        document.getElementById('field_rows').value = field.rows || '';

        handleFieldTypeChange();

        // Handle options for select fields
        const optionsTable = document.getElementById('options-table');
        optionsTable.innerHTML = '';
        
        if (field.type === 'select' && Array.isArray(field.options)) {
            field.options.forEach(option => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <input type="text" class="form-control form-control-sm option-value" 
                               value="${option}" 
                               onchange="updateOptions()">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); updateOptions()">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                optionsTable.appendChild(tr);
            });
            updateOptions();
        }

        modal.show();
    };

    window.deleteField = function(fieldId) {
        if (!confirm('Are you sure you want to delete this field?')) {
            return;
        }

        fetch("{{ route('admin.form-fields.destroy', ['id' => ':id']) }}".replace(':id', fieldId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`[data-field-id="${fieldId}"]`);
                item.remove();
                updateFieldOrder();
            } else {
                alert('Error deleting field: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting field. Please try again.');
        });
    };

    window.addOption = function() {
        const tbody = document.getElementById('options-table');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" class="form-control form-control-sm option-value" onchange="updateOptions()">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); updateOptions()">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        updateOptions();
    };

    window.updateOptions = function() {
        const options = Array.from(document.querySelectorAll('.option-value'))
            .map(input => input.value.trim())
            .filter(Boolean);
        document.getElementById('field_options').value = JSON.stringify(options);
    };

    window.updateFieldOrder = function() {
        const fields = [...document.querySelectorAll('#fields-list .list-group-item')].map((item, index) => ({
            id: item.dataset.fieldId,
            order: index
        }));

        fetch("{{ route('admin.form-fields.order') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ fields: fields.map(f => f.id) })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('Error updating field order: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating field order. Please try again.');
        });
    };
});
</script> 