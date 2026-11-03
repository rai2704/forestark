@extends('layouts.admin')
@section('title', 'Bloco: '.$block->schema()['label'])
@section('content')
<div class="admin-header mb-4">
    <h2><i class="fas fa-th-list me-2"></i>{{ $block->schema()['label'] }}</h2>
    <a href="{{ route('admin.pages.edit', $block->page_id) }}" class="btn-moc-outline btn btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar à página</a>
</div>
<form method="post" action="{{ route('admin.blocks.update', $block) }}" enctype="multipart/form-data" class="admin-form" data-block-form>
    @csrf @method('PUT')
    @include('admin.partials.lang-tabs')
    <div class="card shadow-sm">
        <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
        <div class="card-body">
            <div class="form-check mb-3"><input type="checkbox" name="is_visible" value="1" id="vis" @checked($block->is_visible) class="form-check-input"><label class="form-check-label" for="vis">Visível no site</label></div>
            @include('admin.partials.fields', ['fields' => $block->schema()['fields'], 'data' => $block->data ?? [], 'prefix' => 'data', 'uploads' => 'uploads'])
            <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar bloco</button>
        </div>
    </div>
</form>
@endsection
