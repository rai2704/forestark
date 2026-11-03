@extends('layouts.admin')
@section('title', $newsletter->exists ? 'Editar Newsletter' : 'Nova Newsletter')
@section('content')
<div class="admin-header mb-4">
    <h2><i class="fas fa-paper-plane me-2"></i>{{ $newsletter->exists ? 'Editar' : 'Nova' }} Newsletter</h2>
    <a href="{{ route('admin.newsletters.index') }}" class="btn-moc-outline btn btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
</div>
<form method="post" action="{{ $newsletter->exists ? route('admin.newsletters.update', $newsletter) : route('admin.newsletters.store') }}" class="admin-form">
    @csrf
    @if ($newsletter->exists) @method('PUT') @endif
    @include('admin.partials.lang-tabs')
    <div class="card shadow-sm">
        <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
        <div class="card-body">
            @include('admin.partials.fields', ['fields' => config('portal.newsletter_fields'), 'data' => $newsletter->data ?? [], 'prefix' => 'data', 'uploads' => 'uploads'])
            <p class="form-text">O link de cancelamento é incluído automaticamente em cada e-mail. O envio é feito pela listagem.</p>
            <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar rascunho</button>
        </div>
    </div>
</form>
@endsection
