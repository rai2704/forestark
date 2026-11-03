@extends('layouts.admin')
@section('title', $page->exists ? 'Editar página' : 'Nova página')
@section('content')
    <form method="post" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="space-y-4">
        @csrf
        @if ($page->exists) @method('PUT') @endif
        @include('admin.partials.lang-tabs')
        <div class="adm-card space-y-4">
            @if ($page->slug !== '' || ! $page->exists)
                <div><label class="adm-label">Endereço (ex.: a-empresa)</label><input name="slug" value="{{ old('slug', $page->slug) }}" required pattern="[a-z0-9]+(-[a-z0-9]+)*" class="adm-input"></div>
                <label class="flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published)) class="size-4 accent-emerald-700"> Publicada</label>
            @else
                <p class="text-sm text-slate-500">Esta é a página inicial do site.</p>
            @endif
            @include('admin.partials.fields', ['fields' => config('portal.page_fields'), 'data' => $page->data ?? [], 'prefix' => 'data', 'uploads' => 'uploads'])
            <button class="adm-btn">Salvar página</button>
        </div>
    </form>

    @if ($page->exists)
        <div class="adm-card space-y-3">
            <h2 class="font-semibold">Blocos da página</h2>
            @forelse ($page->blocks as $block)
                <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-200 p-3">
                    <div>
                        <span class="font-medium">{{ $block->schema()['label'] }}</span>
                        @unless ($block->is_visible)<span class="ml-2 rounded bg-slate-200 px-2 py-0.5 text-xs">Oculto</span>@endunless
                    </div>
                    <div class="flex flex-wrap gap-1">
                        <form method="post" action="{{ route('admin.blocks.move', $block) }}">@csrf<input type="hidden" name="direction" value="up"><button class="adm-btn-light" aria-label="Subir">↑</button></form>
                        <form method="post" action="{{ route('admin.blocks.move', $block) }}">@csrf<input type="hidden" name="direction" value="down"><button class="adm-btn-light" aria-label="Descer">↓</button></form>
                        <form method="post" action="{{ route('admin.blocks.toggle', $block) }}">@csrf<button class="adm-btn-light">{{ $block->is_visible ? 'Ocultar' : 'Mostrar' }}</button></form>
                        <form method="post" action="{{ route('admin.blocks.duplicate', $block) }}">@csrf<button class="adm-btn-light">Duplicar</button></form>
                        <a href="{{ route('admin.blocks.edit', $block) }}" class="adm-btn-light">Editar</a>
                        <form method="post" action="{{ route('admin.blocks.destroy', $block) }}" data-confirm="Excluir este bloco?">@csrf @method('DELETE')<button class="adm-btn-danger">Excluir</button></form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">Nenhum bloco ainda.</p>
            @endforelse
            <form method="post" action="{{ route('admin.blocks.store', $page) }}" class="flex flex-wrap gap-2 border-t border-slate-200 pt-3">
                @csrf
                <select name="type" aria-label="Tipo de bloco" class="adm-input max-w-xs">
                    @foreach (config('portal.blocks') as $type => $def)<option value="{{ $type }}">{{ $def['label'] }}</option>@endforeach
                </select>
                <button class="adm-btn">+ Adicionar bloco</button>
            </form>
        </div>
        @if ($page->slug !== '')
            <form method="post" action="{{ route('admin.pages.destroy', $page) }}" data-confirm="Excluir esta página e todos os blocos?">@csrf @method('DELETE')<button class="adm-btn-danger">Excluir página</button></form>
        @endif
    @endif
@endsection
