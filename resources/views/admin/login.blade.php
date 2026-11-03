@extends('layouts.admin-auth')
@section('title', 'Acesso Administrativo')
@section('heading', 'Área Restrita')
@section('subheading', 'Acesso exclusivo para usuários cadastrados')
@section('form')
    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label form-label-login">E-mail</label>
            <div class="input-group input-group-login">
                <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                <input type="email" name="email" id="email" class="form-control" placeholder="Seu e-mail" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
        </div>
        <div class="mb-2">
            <label for="password" class="form-label form-label-login">Senha</label>
            <div class="input-group input-group-login">
                <span class="input-group-text"><i class="fas fa-key"></i></span>
                <input type="password" name="password" id="password" class="form-control" placeholder="Sua senha" required autocomplete="current-password">
                <button type="button" class="btn-toggle-pw" data-toggle-pw="password" aria-label="Mostrar ou ocultar senha"><i class="fas fa-eye"></i></button>
            </div>
        </div>
        <div class="text-end mb-4"><a href="{{ route('admin.password.request') }}" class="link-muted">Esqueceu sua senha?</a></div>
        <div class="d-grid"><button type="submit" class="btn btn-login text-uppercase">Acessar Painel <i class="fas fa-arrow-right ms-2"></i></button></div>
    </form>
@endsection
