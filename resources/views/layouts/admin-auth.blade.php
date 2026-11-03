@php $site = App\Support\Site::settings(); @endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>@yield('title') | {{ $site['site_name'] }}</title>
    @include('partials.admin-head')
    <style>
        body { margin: 0; font-family: 'Roboto', sans-serif; background: #f4f5f0; }
        .login-left { min-height: 100vh; background: linear-gradient(160deg, var(--moc-primary-dk) 0%, var(--moc-primary) 50%, var(--moc-primary) 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; position: relative; overflow: hidden; }
        .login-left::before { content: ''; position: absolute; inset: 0; background: url('{{ media('uploads/site/secao_fundo.png') }}') center/cover no-repeat; opacity: .08; }
        .login-left-content { position: relative; z-index: 1; text-align: center; color: #fff; }
        .login-left-content img { max-width: 220px; margin-bottom: 1.5rem; }
        .login-left-content h1 { font-size: 1.5rem; font-weight: 700; letter-spacing: .04em; margin: 0; }
        .login-badges { display: flex; gap: .5rem; justify-content: center; flex-wrap: wrap; margin-top: 1.25rem; }
        .login-badge { background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25); color: #fff; font-size: .72rem; padding: .25rem .6rem; border-radius: 20px; }
        .login-right { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1.5rem; }
        .login-box { width: 100%; max-width: 420px; }
        .login-title { font-size: 1.35rem; font-weight: 700; color: #1a1a1a; letter-spacing: .03em; text-transform: uppercase; }
        .login-subtitle { font-size: .85rem; color: #6c757d; }
        .login-card { background: #fff; border-radius: .75rem; border-top: 4px solid var(--moc-primary); padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .form-label-login { font-size: .75rem; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: .06em; }
        .input-group-login .input-group-text { background: #fff; border-right: 0; color: #aaa; border-color: #dee2e6; }
        .input-group-login .form-control { border-left: 0; border-color: #dee2e6; font-size: .95rem; }
        .input-group-login .form-control:focus { box-shadow: none; border-color: var(--moc-primary); }
        .input-group-login:focus-within .input-group-text { border-color: var(--moc-primary); }
        .input-group-login .btn-toggle-pw { background: #fff; border: 1px solid #dee2e6; border-left: 0; color: #aaa; padding: 0 .75rem; }
        .input-group-login .btn-toggle-pw:hover { color: var(--moc-primary); }
        .btn-login { background: var(--moc-primary); color: #fff; border: none; padding: .65rem; font-size: .95rem; font-weight: 600; letter-spacing: .04em; border-radius: .4rem; transition: background .2s; }
        .btn-login:hover { background: var(--moc-primary-dk); color: #fff; }
        .link-muted { color: #6c757d; font-size: .8rem; text-decoration: none; }
        .link-muted:hover { color: var(--moc-primary); text-decoration: underline; }
        .login-footer-brand { font-size: .7rem; color: #999; margin-top: 1.5rem; text-align: center; }
    </style>
</head>
<body>
<div class="container-fluid p-0">
<div class="row g-0">
    <div class="col-md-6 d-none d-md-flex">
        <div class="login-left w-100">
            <div class="login-left-content">
                <img src="{{ media($site['logo']) }}" alt="{{ $site['site_name'] }}">
                <h1>{{ $site['site_name'] }}</h1>
                <div class="login-badges">
                    <span class="login-badge"><i class="fas fa-map-marker-alt me-1"></i>{{ $site['city'] }}</span>
                    <span class="login-badge"><i class="fas fa-phone me-1"></i>{{ $site['phone'] }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 login-right">
        <div class="login-box">
            <div class="d-md-none text-center mb-4"><img src="{{ media($site['logo']) }}" alt="{{ $site['site_name'] }}" style="max-width:180px;"></div>
            <div class="text-center mb-4">
                <h2 class="login-title">@yield('heading')</h2>
                <p class="login-subtitle">@yield('subheading')</p>
            </div>
            @if (session('ok'))
                <div role="status" class="alert alert-success py-2 small mb-3">{{ session('ok') }}</div>
            @endif
            @if ($errors->any())
                <div role="alert" class="alert alert-danger py-2 small mb-3"><i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}</div>
            @endif
            <div class="login-card">@yield('form')</div>
            <div class="login-footer-brand">
                <a href="{{ url('/') }}" class="link-muted"><i class="fas fa-arrow-left me-1"></i>Voltar ao site</a>
                &nbsp;·&nbsp; © 1994-{{ date('Y') }} {{ $site['site_name'] }}. Todos os direitos reservados.
            </div>
        </div>
    </div>
</div>
</div>
<script>
document.querySelectorAll('[data-toggle-pw]').forEach(function (b) {
    b.addEventListener('click', function () {
        var pw = document.getElementById(b.dataset.togglePw), show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        b.querySelector('i').className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
    });
});
</script>
</body>
</html>
