@extends('layouts.admin')
@section('title', $block->schema()['label'])
@section('content')
@php $me = auth()->user(); @endphp
<div class="admin-header mb-4">
    <h2><i class="fas fa-th-list me-2"></i>{{ $block->schema()['label'] }}</h2>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="badge {{ $block->is_visible ? 'bg-success' : 'bg-secondary' }}">{{ $block->is_visible ? 'Aparece no site' : 'Oculto no site' }}</span>
        @if ($me->canAccess('pages', 'editar'))
            <form method="post" action="{{ route('admin.blocks.toggle', $block) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-secondary"><i class="fas {{ $block->is_visible ? 'fa-eye-slash' : 'fa-eye' }} me-1"></i>{{ $block->is_visible ? 'Ocultar no site' : 'Mostrar no site' }}</button></form>
        @endif
        <a href="{{ route('admin.pages.blocks', $block->page_id) }}" class="btn-moc-outline btn btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar à página</a>
    </div>
</div>

@if (count($scalars))
    <form method="post" action="{{ route('admin.blocks.update', $block) }}" enctype="multipart/form-data" class="admin-form mb-4" data-block-form>
        @csrf @method('PUT')
        @include('admin.partials.lang-tabs')
        <div class="card shadow-sm">
            <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Dados do bloco</div>
            <div class="card-body">
                @include('admin.partials.fields', ['fields' => $scalars, 'data' => $block->data ?? [], 'prefix' => 'data', 'uploads' => 'uploads'])
                <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar</button>
            </div>
        </div>
    </form>
@endif

@if (! empty($block->schema()['sources']))
    <div class="card shadow-sm mb-4">
        <div class="card-header admin-card-header"><i class="fas fa-database me-2"></i>De onde vêm os itens</div>
        <div class="card-body">
            <form method="post" action="{{ route('admin.blocks.update', $block) }}" class="row g-2 align-items-center" data-source-form>
                @csrf @method('PUT')
                <div class="col-sm-6 col-lg-4">
                    <select name="source" aria-label="Origem dos itens" class="form-select">
                        <option value="">Itens próprios deste bloco</option>
                        @foreach ($block->schema()['sources'] as $key)
                            <option value="{{ $key }}" @selected($block->source() === $key)>Módulo: {{ config("portal.collections.$key.label") }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto"><button class="btn-moc btn"><i class="fas fa-save me-1"></i>Aplicar</button></div>
                @if ($block->source() && $me->canAccess($block->source()))
                    <div class="col-auto"><a href="{{ route('admin.entries.index', $block->source()) }}" class="btn-moc-outline btn"><i class="fas fa-list me-1"></i>Gerenciar {{ config('portal.collections.'.$block->source().'.label') }}</a></div>
                @endif
            </form>
        </div>
    </div>
@endif

@foreach ($repeaters as $field)
    @continue($field['name'] === 'items' && $block->source())
    @php
        $rows = array_values((array) (($block->data ?? [])[$field['name']] ?? []));
        $showImage = collect($field['fields'])->contains('type', 'image');
        $last = count($rows) - 1;
    @endphp
    <div class="card shadow-sm mb-4" data-repeater-card="{{ $field['name'] }}">
        <div class="card-header admin-card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2"></i>{{ $field['label'] }}</span>
            @if ($me->canAccess('pages', 'incluir'))
                <a href="{{ route('admin.blocks.items.create', [$block, $field['name']]) }}" class="btn btn-sm btn-light"><i class="fas fa-plus me-1"></i>Novo</a>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table moc-table mb-0 align-middle">
                <thead><tr><th style="width:60px;">#</th>@if ($showImage)<th style="width:130px;">Imagem</th>@endif<th>Título</th><th class="text-end">Ações</th></tr></thead>
                <tbody>
                    @forelse ($rows as $i => $row)
                        @php $summary = App\Support\Content::summary($field['fields'], (array) $row); @endphp
                        <tr data-item-row>
                            <td>{{ $i + 1 }}</td>
                            @if ($showImage)
                                <td>@if ($summary['image'])<img src="{{ media($summary['image']) }}" alt="" class="media-thumb">@endif</td>
                            @endif
                            <td>{{ $summary['title'] ?: '(sem título)' }} @if (! empty($row['hidden']))<span class="badge bg-secondary">Oculto</span>@endif</td>
                            <td class="text-end text-nowrap">
                                @if ($me->canAccess('pages', 'editar'))
                                    @if ($i > 0)
                                        <form method="post" action="{{ route('admin.blocks.items.move', [$block, $field['name'], $i]) }}" class="d-inline">@csrf<input type="hidden" name="direction" value="up"><button class="btn-action btn-action-view" title="Subir"><i class="fas fa-arrow-up"></i></button></form>
                                    @endif
                                    @if ($i < $last)
                                        <form method="post" action="{{ route('admin.blocks.items.move', [$block, $field['name'], $i]) }}" class="d-inline">@csrf<input type="hidden" name="direction" value="down"><button class="btn-action btn-action-view" title="Descer"><i class="fas fa-arrow-down"></i></button></form>
                                    @endif
                                    <a href="{{ route('admin.blocks.items.edit', [$block, $field['name'], $i]) }}" class="btn-action btn-action-edit" title="Editar"><i class="fas fa-pen"></i></a>
                                @endif
                                @if ($me->canAccess('pages', 'excluir'))
                                    <form method="post" action="{{ route('admin.blocks.items.destroy', [$block, $field['name'], $i]) }}" data-confirm="Excluir este item?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Excluir"><i class="fas fa-trash"></i></button></form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $showImage ? 4 : 3 }}" class="text-center text-muted py-3">Nenhum registro.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endforeach
@endsection
