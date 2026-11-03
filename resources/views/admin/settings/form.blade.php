@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="admin-header mb-4">
    <h2><i class="fas fa-sliders-h me-2"></i>{{ $title }}</h2>
    <a href="{{ route('admin.settings') }}" class="btn-moc-outline btn btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
</div>
<form method="post" action="{{ route('admin.settings.update', $slug) }}" enctype="multipart/form-data" class="admin-form">
    @csrf @method('PUT')
    @if (collect($fields)->contains('tr', true))
        @include('admin.partials.lang-tabs')
    @endif
    <div class="card shadow-sm">
        <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
        <div class="card-body">
            @include('admin.partials.fields', ['fields' => $fields, 'data' => $data, 'prefix' => 'data', 'uploads' => 'uploads'])
            <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar</button>
        </div>
    </div>
</form>
@endsection
