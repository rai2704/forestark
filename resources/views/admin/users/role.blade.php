@extends('layouts.admin')
@section('title', $role->exists ? 'Editar perfil' : 'Novo perfil')
@section('content')
    <form method="post" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="adm-card max-w-xl space-y-4">
        @csrf
        @if ($role->exists) @method('PUT') @endif
        <div><label class="adm-label">Nome</label><input name="name" value="{{ old('name', $role->name) }}" required class="adm-input"></div>
        <fieldset class="space-y-2">
            <legend class="adm-label">Módulos permitidos</legend>
            @foreach (config('portal.modules') as $key => $label)
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, old('permissions', $role->permissions ?? []))) class="size-4 accent-emerald-700"> {{ $label }}</label>
            @endforeach
        </fieldset>
        <button class="adm-btn">Salvar</button>
    </form>
@endsection
