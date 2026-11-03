@extends('layouts.admin')
@section('title', $page->exists ? 'Editar Página' : 'Nova Página')
@section('content')
<div class="admin-header mb-4">
    <h2><i class="fas fa-file-alt me-2"></i>{{ $page->exists ? 'Editar' : 'Nova' }} Página</h2>
    <a href="{{ route('admin.pages.index') }}" class="btn-moc-outline btn btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
</div>
<form method="post" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="admin-form">
    @csrf
    @if ($page->exists) @method('PUT') @endif
    @include('admin.partials.lang-tabs')
    <div class="card shadow-sm">
        <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
        <div class="card-body">
            @if ($page->slug !== '' || ! $page->exists)
                <div class="mb-3"><label class="form-label">Endereço (ex.: a-empresa)</label><input name="slug" value="{{ old('slug', $page->slug) }}" required pattern="[a-z0-9]+(-[a-z0-9]+)*" class="form-control"></div>
                <div class="form-check form-switch mb-3"><input type="checkbox" name="is_published" value="1" id="pub" @checked(old('is_published', $page->is_published)) class="form-check-input"><label class="form-check-label" for="pub">Publicada</label></div>
            @else
                <p class="text-muted small">Esta é a página inicial do site.</p>
            @endif
            @include('admin.partials.fields', ['fields' => config('portal.page_fields'), 'data' => $page->data ?? [], 'prefix' => 'data', 'uploads' => 'uploads'])
            <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar página</button>
        </div>
    </div>
</form>
@endsection
