@extends('layouts.admin')
@section('title', 'Usuários e perfis')
@section('content')
    <div class="adm-card space-y-3">
        <div class="flex items-center justify-between"><h2 class="font-semibold">Usuários</h2><a href="{{ route('admin.users.create') }}" class="adm-btn-light">+ Novo usuário</a></div>
        <table class="adm-table">
            <thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Ativo</th><th></th></tr></thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->role?->name }}</td><td>{{ $user->is_active ? 'Sim' : 'Não' }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.users.edit', $user) }}" class="adm-btn-light">Editar</a>
                            <form method="post" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Excluir este usuário?" class="inline">@csrf @method('DELETE')<button class="adm-btn-danger">Excluir</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="adm-card space-y-3">
        <div class="flex items-center justify-between"><h2 class="font-semibold">Perfis de acesso</h2><a href="{{ route('admin.roles.create') }}" class="adm-btn-light">+ Novo perfil</a></div>
        <table class="adm-table">
            <thead><tr><th>Perfil</th><th>Acesso</th><th>Usuários</th><th></th></tr></thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->full_access ? 'Total' : collect($role->permissions)->map(fn ($m) => config("portal.modules.$m"))->join(', ') }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td class="text-right whitespace-nowrap">
                            @unless ($role->full_access)
                                <a href="{{ route('admin.roles.edit', $role) }}" class="adm-btn-light">Editar</a>
                                <form method="post" action="{{ route('admin.roles.destroy', $role) }}" data-confirm="Excluir este perfil?" class="inline">@csrf @method('DELETE')<button class="adm-btn-danger">Excluir</button></form>
                            @endunless
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
