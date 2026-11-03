@extends('layouts.admin')
@section('title', 'Meu Perfil')
@section('content')
<div class="admin-header mb-4"><h2><i class="fas fa-user-circle me-2"></i>Meu Perfil</h2></div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
    <div class="card-body">
        <form method="post" class="admin-form">
            @csrf @method('PUT')
            <div class="mb-3"><label class="form-label">Nome</label><input name="name" value="{{ old('name', auth()->user()->name) }}" required class="form-control"></div>
            <div class="mb-3"><label class="form-label">E-mail</label><input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="form-control"></div>
            <div class="mb-3"><label class="form-label">Senha atual</label><input type="password" name="current_password" autocomplete="current-password" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Nova senha (mínimo de 10 caracteres)</label><input type="password" name="password" autocomplete="new-password" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Confirmar nova senha</label><input type="password" name="password_confirmation" autocomplete="new-password" class="form-control"></div>
            <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar</button>
        </form>
    </div>
</div>
@endsection
