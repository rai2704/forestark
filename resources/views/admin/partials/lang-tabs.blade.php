@php $langs = App\Models\Language::orderBy('position')->get(); @endphp
<div class="sticky top-0 z-10 flex flex-wrap items-center gap-1 rounded-xl border border-slate-200 bg-white p-2 shadow-sm">
    <span class="px-2 text-sm text-slate-500">Editando o idioma:</span>
    @foreach ($langs as $l)
        <button type="button" data-lang-tab="{{ $l->code }}" class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg px-3 py-1 text-sm font-medium">@if ($l->flagUrl())<img src="{{ $l->flagUrl() }}" alt="" class="h-3 w-4">@endif{{ $l->name }}</button>
    @endforeach
</div>
