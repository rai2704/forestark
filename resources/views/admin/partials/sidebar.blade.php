@php
    $u = auth()->user();
    $act = fn (string ...$names) => request()->routeIs(...$names) ? ' active' : '';
@endphp

<div class="sidebar-group-label"><i class="fas fa-tachometer-alt"></i>Painel</div>
<a href="{{ route('admin.home') }}" class="sidebar-nav-link{{ $act('admin.home') }}"><i class="fas fa-th-large"></i> Dashboard</a>

@if ($u->canAccess('pages') || $u->canAccess('menus'))
    <div class="sidebar-group-label"><i class="fas fa-edit"></i>Conteúdo</div>
    @if ($u->canAccess('pages'))
        <a href="{{ route('admin.pages.index') }}" class="sidebar-nav-link{{ $act('admin.pages.*', 'admin.blocks.*') }}"><i class="fas fa-file-alt"></i> Páginas e Blocos</a>
    @endif
    @if ($u->canAccess('menus'))
        <a href="{{ route('admin.menus.index') }}" class="sidebar-nav-link{{ $act('admin.menus.*') }}"><i class="fas fa-bars"></i> Menus</a>
    @endif
@endif

@if ($u->canAccess('messages'))
    <div class="sidebar-group-label"><i class="fas fa-inbox"></i>Mensagens</div>
    <a href="{{ route('admin.messages.index') }}" class="sidebar-nav-link{{ $act('admin.messages.*') }}"><i class="fas fa-envelope"></i> Contatos</a>
    <a href="{{ route('admin.subscribers') }}" class="sidebar-nav-link{{ $act('admin.subscribers*') }}"><i class="fas fa-user-plus"></i> Inscritos</a>
@endif

@if ($u->canAccess('languages') || $u->canAccess('settings') || $u->canAccess('users'))
    <div class="sidebar-group-label"><i class="fas fa-cog"></i>Sistema</div>
    @if ($u->canAccess('settings'))
        <a href="{{ route('admin.settings') }}" class="sidebar-nav-link{{ $act('admin.settings') }}"><i class="fas fa-sliders-h"></i> Configurações</a>
    @endif
    @if ($u->canAccess('languages'))
        <a href="{{ route('admin.languages.index') }}" class="sidebar-nav-link{{ $act('admin.languages.*') }}"><i class="fas fa-language"></i> Idiomas</a>
    @endif
    @if ($u->canAccess('users'))
        <a href="{{ route('admin.roles.index') }}" class="sidebar-nav-link{{ $act('admin.roles.*') }}"><i class="fas fa-shield-alt"></i> Perfis de Acesso</a>
        <a href="{{ route('admin.users.index') }}" class="sidebar-nav-link{{ $act('admin.users.*') }}"><i class="fas fa-users-cog"></i> Usuários</a>
    @endif
@endif

<form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
    @csrf
    <button type="submit" class="sidebar-nav-link text-danger w-100 text-start border-0 bg-transparent" style="border-left-color:#c0392b;"><i class="fas fa-sign-out-alt"></i> Sair</button>
</form>
