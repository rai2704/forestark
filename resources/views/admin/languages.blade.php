@extends('layouts.admin')
@section('title', 'Idiomas')
@section('content')
    <div class="adm-card overflow-x-auto">
        <table class="adm-table">
            <thead><tr><th>Código</th><th>Bandeira</th><th>Nome</th><th>Ordem</th><th>Ativo</th><th></th></tr></thead>
            <tbody>
                @foreach ($languages as $l)
                    <tr>
                        <td class="font-mono">{{ $l->code }} @if ($l->is_default)<span class="rounded bg-emerald-100 px-2 py-0.5 font-sans text-xs">padrão</span>@endif</td>
                        <td><span class="flex items-center gap-2">@if ($l->flagUrl())<img src="{{ $l->flagUrl() }}" alt="" class="h-4 w-[21px] border border-slate-200">@endif<input form="lang-{{ $l->id }}" name="flag" value="{{ $l->flag }}" required pattern="[a-z]{2}" maxlength="2" aria-label="Código da bandeira" class="adm-input w-16"></span></td>
                        <td><input form="lang-{{ $l->id }}" name="name" value="{{ $l->name }}" required aria-label="Nome" class="adm-input"></td>
                        <td><input form="lang-{{ $l->id }}" type="number" min="0" name="position" value="{{ $l->position }}" aria-label="Ordem" class="adm-input w-20"></td>
                        <td><input form="lang-{{ $l->id }}" type="checkbox" name="is_active" value="1" @checked($l->is_active) @disabled($l->is_default) aria-label="Ativo" class="size-4 accent-emerald-700"></td>
                        <td class="text-right whitespace-nowrap">
                            <form id="lang-{{ $l->id }}" method="post" action="{{ route('admin.languages.update', $l) }}" class="inline">@csrf @method('PUT')<button class="adm-btn-light">Salvar</button></form>
                            @unless ($l->is_default)
                                <form method="post" action="{{ route('admin.languages.destroy', $l) }}" data-confirm="Excluir este idioma?" class="inline">@csrf @method('DELETE')<button class="adm-btn-danger">Excluir</button></form>
                            @endunless
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <form method="post" action="{{ route('admin.languages.store') }}" class="adm-card flex flex-wrap items-end gap-3">
        @csrf
        <div><label class="adm-label">Código (ex.: fr, de, it)</label><input name="code" required pattern="[a-z]{2}(-[A-Z]{2})?" class="adm-input w-32"></div>
        <div><label class="adm-label">Bandeira: código do país (ex.: fr, de, it)</label><input name="flag" required pattern="[a-z]{2}" maxlength="2" class="adm-input w-32"></div>
        <div><label class="adm-label">Nome</label><input name="name" required class="adm-input"></div>
        <button class="adm-btn">Adicionar idioma</button>
    </form>
@endsection
