@extends('layouts.admin')
@section('title', ($index === null ? 'Novo item: ' : 'Editar item: ').$field['label'])
@section('content')
<div class="admin-header mb-4">
    <h2><i class="fas fa-edit me-2"></i>{{ $index === null ? 'Novo' : 'Editar' }}: {{ $field['label'] }}</h2>
    <a href="{{ route('admin.blocks.edit', $block) }}" class="btn-moc-outline btn btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar à listagem</a>
</div>
<form method="post" action="{{ $index === null ? route('admin.blocks.items.store', [$block, $field['name']]) : route('admin.blocks.items.update', [$block, $field['name'], $index]) }}" enctype="multipart/form-data" class="admin-form" data-item-form>
    @csrf
    @if ($index !== null) @method('PUT') @endif
    @include('admin.partials.lang-tabs')
    <div class="card shadow-sm">
        <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
        <div class="card-body">
            @include('admin.partials.fields', ['fields' => $field['fields'], 'data' => $item, 'prefix' => 'data', 'uploads' => 'uploads'])
            <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar</button>
            <a href="{{ route('admin.blocks.edit', $block) }}" class="btn btn-outline-secondary ms-1">Cancelar</a>
        </div>
    </div>
</form>
@endsection
