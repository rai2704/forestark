@extends('layouts.admin')
@section('title', $def['label'])
@section('content')
@php
    $me = auth()->user();
    $dated = $def['dated'] ?? false;
    $showImage = collect($def['fields'])->contains('type', 'image');
    $columns = 3 + ($showImage ? 1 : 0) + ($dated ? 1 : 0);
    $offset = $entries->firstItem() ? $entries->firstItem() - 1 : 0;
@endphp
<div class="admin-header mb-4">
    <h2><i class="fas {{ $def['icon'] }} me-2"></i>{{ $def['label'] }}</h2>
    @if ($me->canAccess($collection, 'incluir'))
        <a href="{{ route('admin.entries.create', $collection) }}" class="btn-moc btn btn-sm"><i class="fas fa-plus me-1"></i>Novo</a>
    @endif
</div>
<div class="card shadow-sm mb-3">
    <div class="card-header admin-card-header"><i class="fas fa-list me-2"></i>Listagem</div>
    <div class="table-responsive">
        <table class="table moc-table mb-0 align-middle">
            <thead><tr>
                @if ($showImage)<th style="width:130px;">Imagem</th>@endif
                <th>Título</th>
                @if ($dated)<th>Data</th>@endif
                <th>Status</th>
                <th class="text-end">Ações</th>
            </tr></thead>
            <tbody>
                @forelse ($entries as $i => $entry)
                    @php $summary = App\Support\Content::summary($def['fields'], $entry->data ?? []); @endphp
                    <tr data-entry-row>
                        @if ($showImage)
                            <td>@if ($summary['image'])<img src="{{ media($summary['image']) }}" alt="" class="media-thumb">@endif</td>
                        @endif
                        <td>{{ $summary['title'] ?: '(sem título)' }}</td>
                        @if ($dated)<td>{{ $entry->published_at?->format('d/m/Y') }}</td>@endif
                        <td><span class="badge {{ $entry->is_published ? 'bg-success' : 'bg-secondary' }}">{{ $entry->is_published ? 'Publicado' : 'Oculto' }}</span></td>
                        <td class="text-end text-nowrap">
                            @if ($me->canAccess($collection, 'editar'))
                                @unless ($dated)
                                    @if ($offset + $i > 0)
                                        <form method="post" action="{{ route('admin.entries.move', [$collection, $entry]) }}" class="d-inline">@csrf<input type="hidden" name="direction" value="up"><button class="btn-action btn-action-view" title="Subir"><i class="fas fa-arrow-up"></i></button></form>
                                    @endif
                                    @if ($offset + $i < $entries->total() - 1)
                                        <form method="post" action="{{ route('admin.entries.move', [$collection, $entry]) }}" class="d-inline">@csrf<input type="hidden" name="direction" value="down"><button class="btn-action btn-action-view" title="Descer"><i class="fas fa-arrow-down"></i></button></form>
                                    @endif
                                @endunless
                                <form method="post" action="{{ route('admin.entries.toggle', [$collection, $entry]) }}" class="d-inline">@csrf<button class="btn-action btn-action-view" title="{{ $entry->is_published ? 'Ocultar' : 'Publicar' }}"><i class="fas {{ $entry->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i></button></form>
                                <a href="{{ route('admin.entries.edit', [$collection, $entry]) }}" class="btn-action btn-action-edit" title="Editar"><i class="fas fa-pen"></i></a>
                            @endif
                            @if ($me->canAccess($collection, 'excluir'))
                                <form method="post" action="{{ route('admin.entries.destroy', [$collection, $entry]) }}" data-confirm="Excluir este registro?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Excluir"><i class="fas fa-trash"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ $columns }}" class="text-center text-muted py-3">Nenhum registro.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $entries->links('pagination::bootstrap-5') }}
@endsection
