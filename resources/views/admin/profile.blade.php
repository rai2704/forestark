@extends('layouts.admin')
@section('title', 'Meu perfil')
@section('content')
    <form method="post" class="adm-card max-w-xl space-y-4">
        @csrf @method('PUT')
        <div><label class="adm-label">Nome</label><input name="name" value="{{ old('name', auth()->user()->name) }}" required class="adm-input"></div>
        <div><label class="adm-label">E-mail</label><input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="adm-input"></div>
        <div><label class="adm-label">Senha atual</label><input type="password" name="current_password" autocomplete="current-password" class="adm-input"></div>
        <div><label class="adm-label">Nova senha (mínimo de 10 caracteres)</label><input type="password" name="password" autocomplete="new-password" class="adm-input"></div>
        <div><label class="adm-label">Confirmar nova senha</label><input type="password" name="password_confirmation" autocomplete="new-password" class="adm-input"></div>
        <button class="adm-btn">Salvar</button>
    </form>
@endsection
