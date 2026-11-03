@extends('layouts.admin-auth')
@section('title', 'Redefinir Senha')
@section('heading', 'Redefinir Senha')
@section('subheading', 'Crie uma nova senha com no mínimo 10 caracteres')
@section('form')
    <form method="POST" action="{{ route('admin.password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3">
            <label for="email" class="form-label form-label-login">E-mail</label>
            <div class="input-group input-group-login">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $email) }}" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label form-label-login">Nova senha</label>
            <div class="input-group input-group-login">
                <span class="input-group-text"><i class="fas fa-key"></i></span>
                <input type="password" name="password" id="password" class="form-control" required minlength="10" autocomplete="new-password">
                <button type="button" class="btn-toggle-pw" data-toggle-pw="password" aria-label="Mostrar ou ocultar senha"><i class="fas fa-eye"></i></button>
            </div>
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="form-label form-label-login">Confirmar nova senha</label>
            <div class="input-group input-group-login">
                <span class="input-group-text"><i class="fas fa-key"></i></span>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password">
            </div>
        </div>
        <div class="d-grid"><button type="submit" class="btn btn-login text-uppercase">Salvar nova senha</button></div>
    </form>
@endsection
