@extends('layouts.admin')
@section('title', 'Usuários')
@section('content')
@php $me = auth()->user(); @endphp
<div class="admin-header mb-4">
    <h2><i class="fas fa-users-cog me-2"></i>Usuários</h2>
    @if ($me->canAccess('users', 'incluir'))
        <a href="{{ route('admin.users.create') }}" class="btn-moc btn btn-sm"><i class="fas fa-plus me-1"></i>Novo</a>
    @endif
</div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-list me-2"></i>Listagem</div>
    <div class="table-responsive">
        <table class="table moc-table mb-0">
            <thead><tr><th>Nome</th><th>E-mail</th><th>Nível</th><th>Status</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
                @foreach ($users as $user)
                    @php $locked = ! $me->role?->full_access && ($user->role?->full_access || $user->is($me)); @endphp
                    <tr>
                        <td>{{ $user->name }}</td><td>{{ $user->email }}</td>
                        <td><span class="badge {{ $user->role?->full_access ? 'bg-danger' : 'bg-secondary' }}">{{ $user->role?->name }}</span></td>
                        <td><span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $user->is_active ? 'Ativo' : 'Inativo' }}</span></td>
                        <td class="text-end text-nowrap">
                            @if (! $locked && $me->canAccess('users', 'editar'))
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn-action btn-action-edit" title="Editar"><i class="fas fa-pen"></i></a>
                            @endif
                            @if (! $locked && $me->canAccess('users', 'excluir'))
                                <form method="post" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Excluir este usuário?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Excluir"><i class="fas fa-trash"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
