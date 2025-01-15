@extends('laravel-admin::layouts.base')

@section('title', ucfirst($slug))

@section('header_buttons')
    <a href="{{ route('admin.create', $slug) }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create New {{ ucfirst(Str::singular($slug)) }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        @foreach($fields as $field => $details)
                            @if($details['show_in_list'] ?? false)
                                <th>{{ $details['label'] }}</th>
                            @endif
                        @endforeach
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                        <tr>
                            @foreach($fields as $field => $details)
                                @if($details['show_in_list'] ?? false)
                                    <td>
                                        @php
                                            $value = $record->$field;
                                        @endphp

                                        @switch($details['type'])
                                            @case('boolean')
                                                @if($value)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-danger">No</span>
                                                @endif
                                                @break
                                            @default
                                                {{ Str::limit($value, 50) }}
                                                @break
                                        @endswitch
                                    </td>
                                @endif
                            @endforeach
                            <td>
                                <a href="{{ route('admin.edit', [$slug, $record->id]) }}" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.destroy', [$slug, $record->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($records->hasPages())
        <div class="mt-4">
            {{ $records->links() }}
        </div>
    @endif
@endsection