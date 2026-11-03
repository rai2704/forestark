@extends('layouts.admin')
@section('title', 'Páginas')
@section('content')
@php $me = auth()->user(); @endphp
<div class="admin-header mb-4">
    <h2><i class="fas fa-file-alt me-2"></i>Páginas</h2>
    @if ($me->canAccess('pages', 'incluir'))
        <a href="{{ route('admin.pages.create') }}" class="btn-moc btn btn-sm"><i class="fas fa-plus me-1"></i>Nova</a>
    @endif
</div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-list me-2"></i>Listagem</div>
    <div class="table-responsive">
        <table class="table moc-table mb-0">
            <thead><tr><th>Título</th><th>Endereço</th><th>Blocos</th><th>Status</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
                @foreach ($pages as $page)
                    <tr data-page="{{ $page->slug === '' ? 'home' : $page->slug }}">
                        <td>{{ $page->content()['title'] }}</td>
                        <td class="text-muted">/{{ $page->slug }}</td>
                        <td>{{ $page->blocks_count }}</td>
                        <td><span class="badge {{ $page->is_published ? 'bg-success' : 'bg-secondary' }}">{{ $page->is_published ? 'Publicada' : 'Rascunho' }}</span></td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.pages.blocks', $page) }}" class="btn-action btn-action-view" title="Blocos da página"><i class="fas fa-th-list"></i></a>
                            @if ($me->canAccess('pages', 'editar'))
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn-action btn-action-edit" title="Editar"><i class="fas fa-pen"></i></a>
                            @endif
                            @if ($page->slug !== '' && $me->canAccess('pages', 'excluir'))
                                <form method="post" action="{{ route('admin.pages.destroy', $page) }}" data-confirm="Excluir esta página e todos os blocos?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Excluir"><i class="fas fa-trash"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
