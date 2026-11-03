@php $langs = App\Models\Language::orderBy('position')->get(); @endphp
<div class="lang-tabs card shadow-sm mb-3">
    <div class="card-body py-2 d-flex flex-wrap align-items-center gap-2">
        <span class="small text-muted"><i class="fas fa-language me-1"></i>Editando o idioma:</span>
        @foreach ($langs as $l)
            <button type="button" data-lang-tab="{{ $l->code }}" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-2">
                @if ($l->flagUrl())<img src="{{ $l->flagUrl() }}" alt="" class="flag-sm">@endif{{ $l->name }}
            </button>
        @endforeach
    </div>
</div>
