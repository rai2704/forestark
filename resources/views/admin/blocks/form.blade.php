@extends('layouts.admin')
@section('title', 'Bloco: '.$block->schema()['label'])
@section('content')
    <a href="{{ route('admin.pages.edit', $block->page_id) }}" class="adm-btn-light">← Voltar à página</a>
    <form method="post" action="{{ route('admin.blocks.update', $block) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')
        @include('admin.partials.lang-tabs')
        <div class="adm-card space-y-4">
            <label class="flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="is_visible" value="1" @checked($block->is_visible) class="size-4 accent-emerald-700"> Visível no site</label>
            @include('admin.partials.fields', ['fields' => $block->schema()['fields'], 'data' => $block->data ?? [], 'prefix' => 'data', 'uploads' => 'uploads'])
            <button class="adm-btn">Salvar bloco</button>
        </div>
    </form>
@endsection
