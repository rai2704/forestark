@php
    $u = auth()->user();
    $act = fn (string ...$names) => request()->routeIs(...$names) ? ' active' : '';
    $current = request()->route('collection');
    $groups = collect(config('portal.collections'))->groupBy('group', preserveKeys: true);
    $entryLink = function (string $key, array $def) use ($current) {
        return '<a href="'.e(route('admin.entries.index', $key)).'" class="sidebar-nav-link'.($current === $key ? ' active' : '').'"><i class="fas '.e($def['icon']).'"></i> '.e($def['label']).'</a>';
    };
    $shortcut = fn (string $slug, string $icon, string $label) => '<a href="'.e(route('admin.shortcut', $slug)).'" class="sidebar-nav-link'.(request()->route('block')?->page?->slug === $slug || request()->route('page')?->slug === $slug ? ' active' : '').'"><i class="fas '.e($icon).'"></i> '.e($label).'</a>';
    $canPages = $u->canAccess('pages');
    $groupIcons = ['Conteúdo' => 'fa-edit', 'A Empresa' => 'fa-building', 'Contato' => 'fa-inbox'];
@endphp

<div class="sidebar-group-label"><i class="fas fa-tachometer-alt"></i>Painel</div>
<a href="{{ route('admin.home') }}" class="sidebar-nav-link{{ $act('admin.home') }}"><i class="fas fa-th-large"></i> Dashboard</a>

@php $conteudo = $groups['Conteúdo']->filter(fn ($d, $k) => $u->canAccess($k)); @endphp
@if ($conteudo->isNotEmpty())
    <div class="sidebar-group-label"><i class="fas fa-edit"></i>Conteúdo</div>
    @foreach ($conteudo as $key => $def){!! $entryLink($key, $def) !!}@endforeach
@endif

@php $empresa = $groups['A Empresa']->filter(fn ($d, $k) => $u->canAccess($k)); @endphp
@if ($canPages || $empresa->isNotEmpty())
    <div class="sidebar-group-label"><i class="fas fa-building"></i>A Empresa</div>
    @if ($canPages)
        {!! $shortcut('sobre-nos', 'fa-circle-info', 'Sobre Nós') !!}
        {!! $shortcut('a-empresa', 'fa-landmark', 'Página A Empresa') !!}
    @endif
    @foreach ($empresa as $key => $def){!! $entryLink($key, $def) !!}@endforeach
@endif

@php $contato = $groups['Contato']->filter(fn ($d, $k) => $u->canAccess($k)); @endphp
@if ($canPages || $contato->isNotEmpty() || $u->canAccess('messages') || $u->canAccess('subscribers') || $u->canAccess('newsletters'))
    <div class="sidebar-group-label"><i class="fas fa-inbox"></i>Contato</div>
    @if ($canPages){!! $shortcut('contato', 'fa-address-card', 'Página de Contato') !!}@endif
    @foreach ($contato as $key => $def){!! $entryLink($key, $def) !!}@endforeach
    @if ($u->canAccess('messages'))
        <a href="{{ route('admin.messages.index') }}" class="sidebar-nav-link{{ $act('admin.messages.*') }}"><i class="fas fa-envelope"></i> Mensagens Recebidas</a>
    @endif
    @if ($u->canAccess('subscribers'))
        <a href="{{ route('admin.subscribers') }}" class="sidebar-nav-link{{ $act('admin.subscribers*') }}"><i class="fas fa-user-plus"></i> Inscritos</a>
    @endif
    @if ($u->canAccess('newsletters'))
        <a href="{{ route('admin.newsletters.index') }}" class="sidebar-nav-link{{ $act('admin.newsletters.*') }}"><i class="fas fa-paper-plane"></i> Newsletter</a>
    @endif
@endif

@if ($canPages || $u->canAccess('menus'))
    <div class="sidebar-group-label"><i class="fas fa-sitemap"></i>Estrutura do Site</div>
    @if ($canPages)
        <a href="{{ route('admin.pages.index') }}" class="sidebar-nav-link{{ $act('admin.pages.*') }}"><i class="fas fa-file-alt"></i> Páginas e Blocos</a>
    @endif
    @if ($u->canAccess('menus'))
        <a href="{{ route('admin.menus.index') }}" class="sidebar-nav-link{{ $act('admin.menus.*') }}"><i class="fas fa-bars"></i> Menus</a>
    @endif
@endif

@if ($u->canAccess('languages') || $u->canAccess('settings') || $u->canAccess('users'))
    <div class="sidebar-group-label"><i class="fas fa-cog"></i>Sistema</div>
    @if ($u->canAccess('settings'))
        <a href="{{ route('admin.settings') }}" class="sidebar-nav-link{{ $act('admin.settings*') }}"><i class="fas fa-sliders-h"></i> Configurações</a>
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
