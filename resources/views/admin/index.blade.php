@extends('laravel-admin::layouts.base')

@section('title', 'Admin - ' . Str::title(str_replace('_', ' ', Str::singular($slug))))

@section('header_buttons')
    <a href="{{ route('admin.create', $slug) }}" class="btn btn-primary">Create New</a>
@endsection

@section('content')
    <div class="my-4">
        <form action="{{ route('admin.index', $slug) }}" method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                <select name="sort" class="form-select">
                    <option value="">Sort By</option>
                    @foreach ($fields as $field => $details)
                        @if (isset($details['show_in_list']) && $details['type'] !== 'relation')
                            <option value="{{ $field }}" {{ request('sort') == $field ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $field)) }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <select name="direction" class="form-select">
                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
                <button type="submit" class="btn btn-outline-secondary">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped mt-3">
                <thead class="table-dark">
                    <tr>
                        @foreach ($fields as $fieldName => $details)
                            @if (isset($details['show_in_list']) && $details['type'] !== 'relation')
                                <th>{{ ucfirst(str_replace('_', ' ', $fieldName)) }}</th>
                            @endif
                        @endforeach
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            @foreach ($fields as $field => $details)
                                @if (isset($details['show_in_list']) && $details['type'] !== 'relation')
                                    <td>
                                        @switch($details['type'])
                                            @case('boolean')
                                                {{ $record->$field ? 'Yes' : 'No' }}
                                                @break
                                            @case('datetime')
                                                @if($record->$field)
                                                    {{ $record->$field->format('Y-m-d H:i:s') }}
                                                @endif
                                                @break
                                            @case('media')
                                                @if ($record->isImage())
                                                    <img width="64" height="64" src="{{ $record->getUrl() }}" alt="Preview" class="d-block m-auto object-fit-cover rounded">
                                                @else
                                                    <i class="d-block m-auto fas fa-4x fa-video"></i>
                                                @endif
                                                @break
                                            
                                            @default
                                                {{ Str::limit($record->$field, 50) }}
                                                @break
                                        @endswitch
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
        </div>

        {{ $records->links() }}
    </div>
@endsection