@extends('laravel-admin::layouts.base')

@section('title', 'Admin - ' . ucfirst($slug))

@section('content')
    <div class="my-4">
        <h1>{{ ucfirst($slug) }} Management</h1>
        <a href="{{ route('admin.create', $slug) }}" class="btn btn-primary">Create New</a>
        
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-dark">
                <tr>
                    @foreach ($fields as $fieldName => $fieldType)
                        <th>
                            {{ ucfirst($fieldName) }}
                            <i class="fas fa-{{ $fieldType }}"></i> <!-- Simplified for example; map $fieldType to icons as needed -->
                        </th>
                    @endforeach
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        @foreach ($fields as $field => $type)
                            <td>
                                @if ($type === 'boolean')
                                    {{ $record->$field ? 'Yes' : 'No' }}
                                @else
                                    {{ $record->$field }}
                                @endif
                            </td>
                        @endforeach
                        <td>
                            <a href="{{ route('admin.edit', [$slug, $record->id]) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.destroy', [$slug, $record->id]) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
