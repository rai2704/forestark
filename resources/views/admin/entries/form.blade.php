@extends('layouts.admin')
@section('title', ($entry->exists ? 'Editar: ' : 'Novo: ').$def['singular'])
@section('content')
<div class="admin-header mb-4">
    <h2><i class="fas {{ $def['icon'] }} me-2"></i>{{ $entry->exists ? 'Editar' : 'Novo' }}: {{ $def['singular'] }}</h2>
    <a href="{{ route('admin.entries.index', $collection) }}" class="btn-moc-outline btn btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar à listagem</a>
</div>
<form method="post" action="{{ $entry->exists ? route('admin.entries.update', [$collection, $entry]) : route('admin.entries.store', $collection) }}" enctype="multipart/form-data" class="admin-form" data-entry-form>
    @csrf
    @if ($entry->exists) @method('PUT') @endif
    @include('admin.partials.lang-tabs')
    <div class="card shadow-sm">
        <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
        <div class="card-body">
            @include('admin.partials.fields', ['fields' => $def['fields'], 'data' => $entry->data ?? [], 'prefix' => 'data', 'uploads' => 'uploads'])

            @if ($def['dated'] ?? false)
                <div class="mb-3"><label class="form-label">Data de publicação</label><input type="date" name="published_at" value="{{ old('published_at', $entry->published_at?->format('Y-m-d')) }}" class="form-control" style="max-width:200px;"></div>
            @endif
            @if ($def['detail'] ?? false)
                <div class="mb-3">
                    <label class="form-label">Endereço da página</label>
                    <div class="input-group"><span class="input-group-text">/{{ $def['detail'] }}/</span><input name="slug" value="{{ old('slug', $entry->slug) }}" class="form-control" placeholder="gerado a partir do título"></div>
                    <p class="form-text mb-0">A página de detalhe só é criada quando o campo "Conteúdo completo" está preenchido.</p>
                </div>
            @endif
            <div class="form-check form-switch mb-3"><input type="checkbox" name="is_published" value="1" id="pub" @checked(old('is_published', $entry->is_published)) class="form-check-input"><label class="form-check-label" for="pub">Publicado no site</label></div>

            <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar</button>
            <a href="{{ route('admin.entries.index', $collection) }}" class="btn btn-outline-secondary ms-1">Cancelar</a>
        </div>
    </div>
</form>
@endsection
