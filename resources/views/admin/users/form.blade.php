@extends('layouts.admin')
@section('title', $user->exists ? 'Editar Usuário' : 'Novo Usuário')
@section('content')
<div class="admin-header mb-4"><h2><i class="fas fa-users-cog me-2"></i>{{ $user->exists ? 'Editar' : 'Novo' }} Usuário</h2></div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
    <div class="card-body">
        <form method="post" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="admin-form">
            @csrf
            @if ($user->exists) @method('PUT') @endif
            <div class="mb-3"><label class="form-label">Nome</label><input name="name" value="{{ old('name', $user->name) }}" required class="form-control"></div>
            <div class="mb-3"><label class="form-label">E-mail</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control"></div>
            <div class="mb-3"><label class="form-label">Perfil</label>
                <select name="role_id" required class="form-select">
                    @foreach ($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>{{ $role->name }}</option>@endforeach
                </select>
            </div>
            <div class="mb-3"><label class="form-label">Senha{{ $user->exists ? ' (deixe em branco para manter)' : '' }}</label><input type="password" name="password" autocomplete="new-password" @required(! $user->exists) class="form-control"></div>
            <div class="mb-3"><label class="form-label">Confirmar senha</label><input type="password" name="password_confirmation" autocomplete="new-password" class="form-control"></div>
            <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" id="act" @checked(old('is_active', $user->is_active)) class="form-check-input"><label class="form-check-label" for="act">Ativo</label></div>
            <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar</button>
        </form>
    </div>
</div>
@endsection
