@extends('laravel-admin::layouts.base')

@section('title', 'Edit Form: ' . $form->name)

@section('header_buttons')
    <a href="{{ route('admin.forms.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Forms
    </a>
@endsection

@section('content')
    @include('laravel-admin::forms.form', ['form' => $form])
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
@endpush 