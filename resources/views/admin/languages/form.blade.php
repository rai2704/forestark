@extends('layouts.admin')
@section('title', $language->exists ? 'Editar Idioma' : 'Novo Idioma')
@section('content')
<div class="admin-header mb-4">
    <h2><i class="fas fa-language me-2"></i>{{ $language->exists ? 'Editar' : 'Novo' }} Idioma</h2>
    <a href="{{ route('admin.languages.index') }}" class="btn-moc-outline btn btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
</div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
    <div class="card-body">
        <form method="post" action="{{ $language->exists ? route('admin.languages.update', $language) : route('admin.languages.store') }}" class="admin-form">
            @csrf
            @if ($language->exists) @method('PUT') @endif
            @unless ($language->exists)
                <div class="mb-3"><label class="form-label">Código do idioma (ex.: fr, de, it)</label><input name="code" value="{{ old('code') }}" required pattern="[a-z]{2}(-[A-Z]{2})?" class="form-control"></div>
            @endunless
            <div class="mb-3"><label class="form-label">Nome</label><input name="name" value="{{ old('name', $language->name) }}" required class="form-control"></div>
            <div class="mb-3">
                <label class="form-label">Bandeira: código do país (ex.: br, us, fr)</label>
                <div class="d-flex align-items-center gap-2">
                    @if ($language->flagUrl())<img src="{{ $language->flagUrl() }}" alt="" class="flag-sm">@endif
                    <input name="flag" value="{{ old('flag', $language->flag) }}" required pattern="[a-z]{2}" maxlength="2" class="form-control" style="max-width:90px;">
                </div>
            </div>
            @if ($language->exists)
                <div class="mb-3"><label class="form-label">Ordem</label><input type="number" min="0" name="position" value="{{ old('position', $language->position) }}" class="form-control" style="max-width:110px;"></div>
            @endif
            <div class="form-check form-switch mb-3">
                @if ($language->is_default)<input type="hidden" name="is_active" value="1">@endif
                <input type="checkbox" name="is_active" value="1" id="act" @checked(old('is_active', $language->is_active)) @disabled($language->is_default) class="form-check-input">
                <label class="form-check-label" for="act">Ativo{{ $language->is_default ? ' (o idioma padrão fica sempre ativo)' : '' }}</label>
            </div>
            <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar</button>
        </form>
    </div>
</div>
@endsection
