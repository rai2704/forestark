@extends('layouts.admin')
@section('title', $item->exists ? 'Editar item' : 'Novo item')
@section('content')
    <form method="post" action="{{ $item->exists ? route('admin.menus.update', $item) : route('admin.menus.store') }}" class="space-y-4">
        @csrf
        @if ($item->exists) @method('PUT') @endif
        <input type="hidden" name="location" value="{{ $item->location }}">
        @include('admin.partials.lang-tabs')
        <div class="adm-card space-y-4">
            @include('admin.partials.fields', ['fields' => config('portal.menu_fields'), 'data' => $item->data ?? [], 'prefix' => 'data', 'uploads' => 'uploads'])
            <label class="flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="is_visible" value="1" @checked($item->is_visible) class="size-4 accent-emerald-700"> Visível</label>
            <button class="adm-btn">Salvar</button>
        </div>
    </form>
@endsection
