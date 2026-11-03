@extends('layouts.admin')
@section('title', $item->exists ? 'Editar Item' : 'Novo Item')
@section('content')
<div class="admin-header mb-4"><h2><i class="fas fa-bars me-2"></i>{{ $item->exists ? 'Editar' : 'Novo' }} Item do Menu</h2></div>
<form method="post" action="{{ $item->exists ? route('admin.menus.update', $item) : route('admin.menus.store') }}" class="admin-form">
    @csrf
    @if ($item->exists) @method('PUT') @endif
    <input type="hidden" name="location" value="{{ $item->location }}">
    @include('admin.partials.lang-tabs')
    <div class="card shadow-sm">
        <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
        <div class="card-body">
            @include('admin.partials.fields', ['fields' => config('portal.menu_fields'), 'data' => $item->data ?? [], 'prefix' => 'data', 'uploads' => 'uploads'])
            <div class="form-check mb-3"><input type="checkbox" name="is_visible" value="1" id="vis" @checked($item->is_visible) class="form-check-input"><label class="form-check-label" for="vis">Visível</label></div>
            <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar</button>
        </div>
    </div>
</form>
@endsection
