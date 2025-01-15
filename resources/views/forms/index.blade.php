@extends('laravel-admin::layouts.base')

@section('title', 'Forms')

@section('header_buttons')
    <a href="{{ route('admin.forms.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create New Form
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Fields</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($forms as $form)
                        <tr>
                            <td>{{ $form->name }}</td>
                            <td>{{ $form->slug }}</td>
                            <td>{{ $form->fields->count() }}</td>
                            <td>
                                <a href="{{ route('admin.forms.edit', $form->id) }}" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.forms.destroy', $form->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Are you sure you want to delete this form?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3">No forms found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($forms->hasPages())
        <div class="mt-4">
            {{ $forms->links() }}
        </div>
    @endif
@endsection 