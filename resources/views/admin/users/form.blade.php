@extends('layouts.admin')
@section('title', $user->exists ? 'Editar usuário' : 'Novo usuário')
@section('content')
    <form method="post" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="adm-card max-w-xl space-y-4">
        @csrf
        @if ($user->exists) @method('PUT') @endif
        <div><label class="adm-label">Nome</label><input name="name" value="{{ old('name', $user->name) }}" required class="adm-input"></div>
        <div><label class="adm-label">E-mail</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="adm-input"></div>
        <div><label class="adm-label">Perfil</label>
            <select name="role_id" required class="adm-input">
                @foreach ($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>{{ $role->name }}</option>@endforeach
            </select>
        </div>
        <div><label class="adm-label">Senha{{ $user->exists ? ' (deixe em branco para manter)' : '' }}</label><input type="password" name="password" autocomplete="new-password" @required(! $user->exists) class="adm-input"></div>
        <div><label class="adm-label">Confirmar senha</label><input type="password" name="password_confirmation" autocomplete="new-password" class="adm-input"></div>
        <label class="flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active)) class="size-4 accent-emerald-700"> Ativo</label>
        <button class="adm-btn">Salvar</button>
    </form>
@endsection
