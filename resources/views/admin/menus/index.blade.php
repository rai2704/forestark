@extends('layouts.admin')
@section('title', 'Menus')
@section('content')
    @foreach ($locations as $location => $label)
        <div class="adm-card space-y-3">
            <div class="flex items-center justify-between"><h2 class="font-semibold">{{ $label }}</h2><a href="{{ route('admin.menus.create', ['location' => $location]) }}" class="adm-btn-light">+ Adicionar</a></div>
            @foreach ($items[$location] ?? [] as $item)
                <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-200 p-3">
                    <div>{{ $item->content()['label'] }} <span class="text-sm text-slate-500">{{ $item->content()['url'] }}</span> @unless ($item->is_visible)<span class="rounded bg-slate-200 px-2 py-0.5 text-xs">Oculto</span>@endunless</div>
                    <div class="flex gap-1">
                        <form method="post" action="{{ route('admin.menus.move', $item) }}">@csrf<input type="hidden" name="direction" value="up"><button class="adm-btn-light" aria-label="Subir">↑</button></form>
                        <form method="post" action="{{ route('admin.menus.move', $item) }}">@csrf<input type="hidden" name="direction" value="down"><button class="adm-btn-light" aria-label="Descer">↓</button></form>
                        <a href="{{ route('admin.menus.edit', $item) }}" class="adm-btn-light">Editar</a>
                        <form method="post" action="{{ route('admin.menus.destroy', $item) }}" data-confirm="Excluir este item?">@csrf @method('DELETE')<button class="adm-btn-danger">Excluir</button></form>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
