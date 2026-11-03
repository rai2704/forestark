@extends('layouts.admin')
@section('title', $page->exists ? 'Editar Página' : 'Nova Página')
@section('content')
<div class="admin-header mb-4"><h2><i class="fas fa-file-alt me-2"></i>{{ $page->exists ? 'Editar' : 'Nova' }} Página</h2></div>
<form method="post" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="admin-form">
    @csrf
    @if ($page->exists) @method('PUT') @endif
    @include('admin.partials.lang-tabs')
    <div class="card shadow-sm mb-4">
        <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
        <div class="card-body">
            @if ($page->slug !== '' || ! $page->exists)
                <div class="mb-3"><label class="form-label">Endereço (ex.: a-empresa)</label><input name="slug" value="{{ old('slug', $page->slug) }}" required pattern="[a-z0-9]+(-[a-z0-9]+)*" class="form-control"></div>
                <div class="form-check mb-3"><input type="checkbox" name="is_published" value="1" id="pub" @checked(old('is_published', $page->is_published)) class="form-check-input"><label class="form-check-label" for="pub">Publicada</label></div>
            @else
                <p class="text-muted small">Esta é a página inicial do site.</p>
            @endif
            @include('admin.partials.fields', ['fields' => config('portal.page_fields'), 'data' => $page->data ?? [], 'prefix' => 'data', 'uploads' => 'uploads'])
            <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar página</button>
        </div>
    </div>
</form>

@if ($page->exists)
    <div class="card shadow-sm mb-4">
        <div class="card-header admin-card-header"><i class="fas fa-th-list me-2"></i>Blocos da página</div>
        <div class="table-responsive">
            <table class="table moc-table mb-0">
                <thead><tr><th>Bloco</th><th>Status</th><th class="text-end">Ações</th></tr></thead>
                <tbody>
                    @forelse ($page->blocks as $block)
                        <tr data-block-row>
                            <td>{{ $block->schema()['label'] }}</td>
                            <td><span class="badge {{ $block->is_visible ? 'bg-success' : 'bg-secondary' }}">{{ $block->is_visible ? 'Visível' : 'Oculto' }}</span></td>
                            <td class="text-end text-nowrap">
                                <form method="post" action="{{ route('admin.blocks.move', $block) }}" class="d-inline">@csrf<input type="hidden" name="direction" value="up"><button class="btn-action btn-action-view" title="Subir"><i class="fas fa-arrow-up"></i></button></form>
                                <form method="post" action="{{ route('admin.blocks.move', $block) }}" class="d-inline">@csrf<input type="hidden" name="direction" value="down"><button class="btn-action btn-action-view" title="Descer"><i class="fas fa-arrow-down"></i></button></form>
                                <form method="post" action="{{ route('admin.blocks.toggle', $block) }}" class="d-inline">@csrf<button class="btn-action btn-action-view" title="{{ $block->is_visible ? 'Ocultar' : 'Mostrar' }}"><i class="fas {{ $block->is_visible ? 'fa-eye-slash' : 'fa-eye' }}"></i></button></form>
                                <form method="post" action="{{ route('admin.blocks.duplicate', $block) }}" class="d-inline">@csrf<button class="btn-action btn-action-view" title="Duplicar"><i class="fas fa-copy"></i></button></form>
                                <a href="{{ route('admin.blocks.edit', $block) }}" class="btn-action btn-action-edit" title="Editar"><i class="fas fa-pen"></i></a>
                                <form method="post" action="{{ route('admin.blocks.destroy', $block) }}" data-confirm="Excluir este bloco?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Excluir"><i class="fas fa-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Nenhum bloco ainda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">
            <form method="post" action="{{ route('admin.blocks.store', $page) }}" class="row g-2 align-items-center">
                @csrf
                <div class="col-sm-6 col-lg-4">
                    <select name="type" aria-label="Tipo de bloco" class="form-select">
                        @foreach (config('portal.blocks') as $type => $def)<option value="{{ $type }}">{{ $def['label'] }}</option>@endforeach
                    </select>
                </div>
                <div class="col-auto"><button class="btn-moc btn"><i class="fas fa-plus me-1"></i>Adicionar bloco</button></div>
            </form>
        </div>
    </div>
    @if ($page->slug !== '')
        <form method="post" action="{{ route('admin.pages.destroy', $page) }}" data-confirm="Excluir esta página e todos os blocos?">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash me-1"></i>Excluir página</button></form>
    @endif
@endif
@endsection
