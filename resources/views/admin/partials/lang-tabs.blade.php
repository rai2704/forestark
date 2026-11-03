@php $langs = App\Models\Language::orderBy('position')->get(); @endphp
<div class="sticky top-0 z-10 flex flex-wrap items-center gap-1 rounded-xl border border-slate-200 bg-white p-2 shadow-sm">
    <span class="px-2 text-sm text-slate-500">Editando o idioma:</span>
    @foreach ($langs as $l)
        <button type="button" data-lang-tab="{{ $l->code }}" class="cursor-pointer rounded-lg px-3 py-1 text-sm font-medium">{{ $l->name }}</button>
    @endforeach
</div>
