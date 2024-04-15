@extends('laravel-admin::layouts.base')

@section('title', 'Admin - ' . (isset($record) ? 'Edit' : 'Create') . ' ' . ucfirst(Str::singular($model)))

@section('content')
    <div class="my-4">
        <h1>{{ isset($record) ? 'Edit' : 'Create' }} {{ ucfirst(Str::singular($model)) }}</h1>
        <form method="POST" action="{{ isset($record) ? route('admin.update', [$model, $record->id]) : route('admin.store', $model) }}" class="mt-4">
            @csrf
            @if (isset($record))
                @method('PUT')
            @endif

            @foreach ($fields as $field => $type)
                @include('laravel-admin::partials.input', ['type' => $type, 'field' => $field, 'value' => old($field, $record->$field ?? '')])
            @endforeach

            <button type="submit" class="btn btn-primary">{{ isset($record) ? 'Update' : 'Create' }}</button>
        </form>
    </div>
@endsection
