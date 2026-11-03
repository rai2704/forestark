@extends('layouts.admin')
@section('title', 'Blocos: '.$page->content()['title'])
@section('content')
@php $me = auth()->user(); @endphp
<div class="admin-header mb-4">
    <h2><i class="fas fa-th-list me-2"></i>Blocos: {{ $page->content()['title'] }}</h2>
    <a href="{{ route('admin.pages.index') }}" class="btn-moc-outline btn btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
</div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-list me-2"></i>Listagem</div>
    <div class="table-responsive">
        <table class="table moc-table mb-0">
            <thead><tr><th>Bloco</th><th>Status</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
                @forelse ($page->blocks as $block)
                    <tr data-block-row>
                        <td>{{ $block->schema()['label'] }}</td>
                        <td><span class="badge {{ $block->is_visible ? 'bg-success' : 'bg-secondary' }}">{{ $block->is_visible ? 'Visível' : 'Oculto' }}</span></td>
                        <td class="text-end text-nowrap">
                            @if ($me->canAccess('pages', 'editar'))
                                <form method="post" action="{{ route('admin.blocks.move', $block) }}" class="d-inline">@csrf<input type="hidden" name="direction" value="up"><button class="btn-action btn-action-view" title="Subir"><i class="fas fa-arrow-up"></i></button></form>
                                <form method="post" action="{{ route('admin.blocks.move', $block) }}" class="d-inline">@csrf<input type="hidden" name="direction" value="down"><button class="btn-action btn-action-view" title="Descer"><i class="fas fa-arrow-down"></i></button></form>
                                <form method="post" action="{{ route('admin.blocks.toggle', $block) }}" class="d-inline">@csrf<button class="btn-action btn-action-view" title="{{ $block->is_visible ? 'Ocultar' : 'Mostrar' }}"><i class="fas {{ $block->is_visible ? 'fa-eye-slash' : 'fa-eye' }}"></i></button></form>
                            @endif
                            @if ($me->canAccess('pages', 'incluir'))
                                <form method="post" action="{{ route('admin.blocks.duplicate', $block) }}" class="d-inline">@csrf<button class="btn-action btn-action-view" title="Duplicar"><i class="fas fa-copy"></i></button></form>
                            @endif
                            @if ($me->canAccess('pages', 'editar'))
                                <a href="{{ route('admin.blocks.edit', $block) }}" class="btn-action btn-action-edit" title="Editar"><i class="fas fa-pen"></i></a>
                            @endif
                            @if ($me->canAccess('pages', 'excluir'))
                                <form method="post" action="{{ route('admin.blocks.destroy', $block) }}" data-confirm="Excluir este bloco?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Excluir"><i class="fas fa-trash"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">Nenhum bloco ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($me->canAccess('pages', 'incluir'))
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
    @endif
</div>
@endsection
