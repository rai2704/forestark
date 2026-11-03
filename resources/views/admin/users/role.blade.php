@extends('layouts.admin')
@section('title', $role->exists ? 'Editar Perfil' : 'Novo Perfil')
@section('content')
<div class="admin-header mb-4"><h2><i class="fas fa-shield-alt me-2"></i>{{ $role->exists ? 'Editar' : 'Novo' }} Perfil</h2></div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
    <div class="card-body">
        <form method="post" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="admin-form">
            @csrf
            @if ($role->exists) @method('PUT') @endif
            <div class="mb-3"><label class="form-label">Nome</label><input name="name" value="{{ old('name', $role->name) }}" required class="form-control"></div>
            <fieldset class="mb-3">
                <legend class="form-label">Módulos permitidos</legend>
                @foreach (config('portal.modules') as $key => $label)
                    <div class="form-check"><input type="checkbox" name="permissions[]" value="{{ $key }}" id="m-{{ $key }}" @checked(in_array($key, old('permissions', $role->permissions ?? []))) class="form-check-input"><label class="form-check-label" for="m-{{ $key }}">{{ $label }}</label></div>
                @endforeach
            </fieldset>
            <button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar</button>
        </form>
    </div>
</div>
@endsection
