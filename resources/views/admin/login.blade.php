<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Entrar | Admin</title>
    @vite(['resources/css/admin.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-emerald-950 p-4 font-sans">
    <form method="post" class="adm-card w-full max-w-sm space-y-4">
        @csrf
        <h1 class="text-xl font-bold text-slate-800">Área administrativa</h1>
        @error('email')<p role="alert" class="rounded-lg bg-red-100 px-3 py-2 text-sm text-red-900">{{ $message }}</p>@enderror
        <div><label for="email" class="adm-label">E-mail</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="adm-input"></div>
        <div><label for="password" class="adm-label">Senha</label><input id="password" type="password" name="password" required class="adm-input"></div>
        <button class="adm-btn w-full justify-center">Entrar</button>
    </form>
</body>
</html>
