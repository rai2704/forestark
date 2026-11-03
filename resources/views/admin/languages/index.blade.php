@extends('layouts.admin')
@section('title', 'Idiomas')
@section('content')
@php $me = auth()->user(); @endphp
<div class="admin-header mb-4">
    <h2><i class="fas fa-language me-2"></i>Idiomas</h2>
    @if ($me->canAccess('languages', 'incluir'))
        <a href="{{ route('admin.languages.create') }}" class="btn-moc btn btn-sm"><i class="fas fa-plus me-1"></i>Novo</a>
    @endif
</div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-list me-2"></i>Listagem</div>
    <div class="table-responsive">
        <table class="table moc-table mb-0 align-middle">
            <thead><tr><th>Bandeira</th><th>Nome</th><th>Código</th><th>Ordem</th><th>Status</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
                @foreach ($languages as $l)
                    <tr>
                        <td>@if ($l->flagUrl())<img src="{{ $l->flagUrl() }}" alt="" class="flag-sm">@endif</td>
                        <td>{{ $l->name }} @if ($l->is_default)<span class="badge bg-success">padrão</span>@endif</td>
                        <td><code>{{ $l->code }}</code></td>
                        <td>{{ $l->position }}</td>
                        <td><span class="badge {{ $l->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $l->is_active ? 'Ativo' : 'Inativo' }}</span></td>
                        <td class="text-end text-nowrap">
                            @if ($me->canAccess('languages', 'editar'))
                                <a href="{{ route('admin.languages.edit', $l) }}" class="btn-action btn-action-edit" title="Editar"><i class="fas fa-pen"></i></a>
                            @endif
                            @if (! $l->is_default && $me->canAccess('languages', 'excluir'))
                                <form method="post" action="{{ route('admin.languages.destroy', $l) }}" data-confirm="Excluir este idioma?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Excluir"><i class="fas fa-trash"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
