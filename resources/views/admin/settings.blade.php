@extends('layouts.admin')
@section('title', 'Configurações')
@section('content')
<div class="admin-header mb-4"><h2><i class="fas fa-sliders-h me-2"></i>Configurações</h2></div>
<form method="post" enctype="multipart/form-data" class="admin-form">
    @csrf @method('PUT')
    @include('admin.partials.lang-tabs')
    @foreach ($groups as $title => $fields)
        <div class="card shadow-sm mb-4">
            <div class="card-header admin-card-header"><i class="fas fa-cog me-2"></i>{{ $title }}</div>
            <div class="card-body">
                @include('admin.partials.fields', ['fields' => $fields, 'data' => $data, 'prefix' => 'data', 'uploads' => 'uploads'])
            </div>
        </div>
    @endforeach
    <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar configurações</button>
</form>
@endsection
