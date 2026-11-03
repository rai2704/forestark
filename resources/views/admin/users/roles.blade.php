@extends('layouts.admin')
@section('title', 'Perfis de Acesso')
@section('content')
<div class="admin-header mb-4">
    <h2><i class="fas fa-shield-alt me-2"></i>Perfis de Acesso</h2>
    <a href="{{ route('admin.roles.create') }}" class="btn-moc btn btn-sm"><i class="fas fa-plus me-1"></i>Novo</a>
</div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-list me-2"></i>Listagem</div>
    <div class="table-responsive">
        <table class="table moc-table mb-0">
            <thead><tr><th>Perfil</th><th>Acesso</th><th>Usuários</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->full_access ? 'Total' : collect($role->permissions)->map(fn ($m) => config("portal.modules.$m"))->join(', ') }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td class="text-end text-nowrap">
                            @unless ($role->full_access)
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn-action btn-action-edit" title="Editar"><i class="fas fa-pen"></i></a>
                                <form method="post" action="{{ route('admin.roles.destroy', $role) }}" data-confirm="Excluir este perfil?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Excluir"><i class="fas fa-trash"></i></button></form>
                            @endunless
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
