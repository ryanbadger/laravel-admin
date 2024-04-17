@extends('laravel-admin::layouts.base')

@section('title', 'Admin - ' . Str::title(str_replace('_', ' ', Str::singular($slug))))

@section('content')
    <div class="my-4">
        <h1>{{ Str::title(str_replace('_', ' ', Str::singular($slug))) }} Management</h1>
        <a href="{{ route('admin.create', $slug) }}" class="btn btn-primary">Create New</a>
        
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-dark">
                <tr>
                    @foreach ($modelConfig['fields'] as $fieldName => $details)
                        @if ($details['show_in_list'])
                            <th>
                                {{ ucfirst(str_replace('_', ' ', $fieldName)) }}
                                <i class="fas fa-{{ $details['type'] }}"></i> <!-- Update icon logic based on actual field types or remove if unnecessary -->
                            </th>
                        @endif
                    @endforeach 
                    <th>Actions</th>
                </tr>                
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        @foreach ($modelConfig['fields'] as $field => $details)
                            @if ($details['show_in_list'])
                                <td>
                                    @if ($details['type'] === 'boolean')
                                        {{ $record->$field ? 'Yes' : 'No' }}
                                    @elseif ($details['type'] === 'datetime' && $record->$field)
                                        {{ $record->$field->format('Y-m-d H:i:s') }} <!-- Formatting datetime -->
                                    @else
                                        {{ Str::limit($record->$field, 50) }} <!-- Adjust limit as necessary -->
                                    @endif
                                </td>
                            @endif
                        @endforeach
                        <td>
                            <a href="{{ route('admin.edit', [$slug, $record->id]) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('admin.destroy', [$slug, $record->id]) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $records->links() }}  {{-- Pagination links --}}

    </div>
@endsection
