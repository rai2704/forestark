@php
    $u = auth()->user();
    $nav = [
        ['admin.home', 'Painel', null],
        ['admin.pages.index', 'Páginas e blocos', 'pages'],
        ['admin.menus.index', 'Menus', 'menus'],
        ['admin.languages.index', 'Idiomas', 'languages'],
        ['admin.settings', 'Aparência e configurações', 'settings'],
        ['admin.messages.index', 'Mensagens', 'messages'],
        ['admin.subscribers', 'Inscritos', 'messages'],
        ['admin.users.index', 'Usuários e perfis', 'users'],
    ];
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title', 'Painel') | Admin</title>
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 lg:flex">
    <aside class="bg-emerald-950 text-emerald-50 lg:min-h-screen lg:w-64 lg:shrink-0">
        <div class="border-b border-white/10 px-5 py-4 font-bold">Forest Ark <span class="font-normal text-emerald-300">Admin</span></div>
        <nav class="flex flex-wrap gap-1 p-3 lg:flex-col">
            @foreach ($nav as [$route, $label, $module])
                @if (! $module || $u->canAccess($module))
                    <a href="{{ route($route) }}" @class(['rounded-lg px-3 py-2 text-sm hover:bg-white/10', 'bg-white/15 font-semibold' => request()->routeIs(str_replace('.index', '.*', $route))])>{{ $label }}</a>
                @endif
            @endforeach
        </nav>
    </aside>
    <div class="min-w-0 flex-1">
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-white px-6 py-3">
            <h1 class="text-lg font-semibold">@yield('title', 'Painel')</h1>
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ url('/') }}" target="_blank" class="adm-btn-light">Ver site</a>
                <a href="{{ route('admin.profile') }}" class="adm-btn-light">{{ $u->name }}</a>
                <form action="{{ route('admin.logout') }}" method="post">@csrf<button class="adm-btn-light">Sair</button></form>
            </div>
        </header>
        <main class="mx-auto max-w-6xl space-y-5 p-6">
            @if (session('ok'))<p role="status" class="rounded-lg bg-emerald-100 px-4 py-3 text-sm text-emerald-900">{{ session('ok') }}</p>@endif
            @if ($errors->any())
                <ul role="alert" class="rounded-lg bg-red-100 px-4 py-3 text-sm text-red-900">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
