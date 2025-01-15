<form action="{{ $form->exists ? route('admin.forms.update', $form->id) : route('admin.forms.store') }}" method="POST">
    @csrf
    @if($form->exists)
        @method('PUT')
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <!-- Basic Form Information -->
            <div class="mb-3">
                <label for="name" class="form-label">Form Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $form->name) }}" 
                    class="form-control @error('name') is-invalid @enderror">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $form->slug) }}" 
                    class="form-control @error('slug') is-invalid @enderror">
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="3" 
                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $form->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Form Fields Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="card-title mb-0">Form Fields</h3>
                <button type="button" onclick="addField()" class="btn btn-primary">
                    Add Field
                </button>
            </div>

            <div id="fields-container">
                @foreach(old('fields', $form->fields ?? []) as $index => $field)
                    @include('laravel-admin::forms.field', [
                        'field' => $field,
                        'index' => $index,
                        'fieldTypes' => $fieldTypes
                    ])
                @endforeach
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary me-2">
            Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            {{ $form->exists ? 'Update Form' : 'Create Form' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    function addField() {
        const container = document.getElementById('fields-container');
        const index = container.children.length;
        
        fetch('{{ route('admin.forms.field-template') }}?index=' + index)
            .then(response => response.text())
            .then(html => {
                container.insertAdjacentHTML('beforeend', html);
            });
    }

    function removeField(button) {
        button.closest('.field-container').remove();
        reorderFields();
    }

    function reorderFields() {
        const fields = document.querySelectorAll('.field-container');
        fields.forEach((field, index) => {
            field.querySelectorAll('[name^="fields["]').forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace(/fields\[\d+\]/, `fields[${index}]`));
            });
        });
    }

    // Enable drag and drop reordering
    new Sortable(document.getElementById('fields-container'), {
        animation: 150,
        handle: '.drag-handle',
        onEnd: reorderFields
    });
</script>
@endpush

@push('styles')
<style>
    .field-container {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 1rem;
        margin-bottom: 1rem;
        position: relative;
    }
    .field-container:hover {
        background-color: #e9ecef;
    }
    .drag-handle {
        cursor: move;
    }
</style>
@endpush 