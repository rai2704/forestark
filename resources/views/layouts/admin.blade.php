@php
    $site = App\Support\Site::settings();
    $u = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>@yield('title', 'Painel') | Admin | {{ $site['site_name'] }}</title>
    @include('partials.admin-head')
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
</head>
<body class="admin-body">

<nav class="admin-navbar">
    <a href="{{ route('admin.home') }}" class="brand">
        <img src="{{ media($site['logo']) }}" alt="{{ $site['site_name'] }}">
        <div class="brand-text">
            <span class="brand-main">{{ $site['site_name'] }}</span>
            <span class="brand-sub">Área Administrativa</span>
        </div>
    </a>
    <div class="nav-icons">
        <div class="dropdown">
            <button class="nav-icon-btn dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Acessibilidade" title="Acessibilidade"><i class="fas fa-universal-access"></i></button>
            <div class="dropdown-menu dropdown-menu-end p-2 shadow" style="min-width:190px;">
                <p class="dropdown-header px-1 py-0 mb-1" style="font-size:.64rem;">TAMANHO</p>
                <div class="d-flex gap-1 px-1 mb-2">
                    <button type="button" data-a11y-font="-1" class="btn btn-sm btn-outline-secondary flex-fill" aria-label="{{ __('site.font_down') }}">A-</button>
                    <button type="button" data-a11y-font="0" class="btn btn-sm btn-outline-secondary flex-fill" aria-label="{{ __('site.font_reset') }}">A</button>
                    <button type="button" data-a11y-font="1" class="btn btn-sm btn-outline-secondary flex-fill fw-bold" aria-label="{{ __('site.font_up') }}">A+</button>
                </div>
                <hr class="dropdown-divider my-1">
                <button type="button" data-a11y-contrast aria-pressed="false" class="dropdown-item rounded py-1 small"><i class="fas fa-circle-half-stroke me-2"></i>Alto Contraste</button>
            </div>
        </div>
        <a href="{{ url('/') }}" class="nav-icon-btn" title="Ver Site" aria-label="Ver Site" target="_blank" rel="noopener"><i class="fas fa-globe"></i></a>
        <button class="nav-icon-btn d-lg-none" id="sidebarToggle" aria-label="Menu"><i class="fas fa-bars"></i></button>
        <div class="dropdown">
            <button class="nav-icon-btn dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle me-1"></i>
                <span class="d-none d-sm-inline">{{ $u->name }}</span>
                <span class="badge ms-1 {{ $u->role?->full_access ? 'bg-danger' : 'bg-secondary' }}" style="font-size:.6rem;vertical-align:middle;">{{ $u->role?->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:200px;">
                <li><h6 class="dropdown-header text-truncate" style="max-width:190px;">{{ $u->name }}</h6></li>
                <li><span class="dropdown-item-text text-muted small py-0">{{ $u->role?->name }}</span></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="fas fa-user-circle me-2"></i>Meu Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Sair</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="admin-wrapper">
    <aside class="admin-sidebar" id="adminSidebar">
        @include('admin.partials.sidebar')
    </aside>
    <div class="admin-content">
        @if (session('ok'))
            <div role="status" class="alert alert-success alert-auto-dismiss alert-dismissible fade show">{{ session('ok') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button></div>
        @endif
        @if ($errors->any())
            <div role="alert" class="alert alert-danger alert-dismissible fade show">
                @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<footer class="admin-footer">
    <span>© 1994-{{ date('Y') }} {{ $site['site_name'] }}. Todos os direitos reservados.</span>
    <span>{{ config('portal.developer.name') }} — {{ config('portal.developer.slogan') }}</span>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2/dist/js/tom-select.complete.min.js"></script>
<script>document.querySelectorAll('select.form-select').forEach(function (s) { new TomSelect(s, { create: false, maxOptions: null }); });</script>
<div vw class="enabled"><div vw-access-button class="active"></div><div vw-plugin-wrapper><div class="vw-plugin-top-wrapper"></div></div></div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>new window.VLibras.Widget('https://vlibras.gov.br/app');</script>
</body>
</html>
