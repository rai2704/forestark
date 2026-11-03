@extends('layouts.admin-auth')
@section('title', 'Recuperar Senha')
@section('heading', 'Recuperar Senha')
@section('subheading', 'Informe seu e-mail para receber o link de redefinição')
@section('form')
    <form method="POST">
        @csrf
        <div class="mb-4">
            <label for="email" class="form-label form-label-login">E-mail</label>
            <div class="input-group input-group-login">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
        </div>
        <div class="d-grid mb-3"><button type="submit" class="btn btn-login text-uppercase">Enviar link</button></div>
        <div class="text-center"><a href="{{ route('admin.login') }}" class="link-muted">Voltar ao login</a></div>
    </form>
@endsection
