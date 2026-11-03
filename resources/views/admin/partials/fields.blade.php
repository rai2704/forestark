{{-- Formulário gerado pelo esquema. $fields, $data, $prefix (data[...]), $uploads (uploads[...]) --}}
@php $langs = App\Models\Language::orderBy('position')->get(); @endphp
@foreach ($fields as $f)
    @php
        $name = $prefix.'['.$f['name'].']';
        $upload = $uploads.'['.$f['name'].']';
        $value = $data[$f['name']] ?? null;
    @endphp

    @if ($f['type'] === 'repeater')
        <div data-repeater class="space-y-3">
            <h3 class="font-semibold">{{ $f['label'] }}</h3>
            <div data-repeater-list class="space-y-3">
                @foreach (array_values((array) $value) as $i => $item)
                    @include('admin.partials.repeater-item', ['f' => $f, 'item' => (array) $item, 'i' => $i, 'name' => $name, 'upload' => $upload])
                @endforeach
            </div>
            <template>@include('admin.partials.repeater-item', ['f' => $f, 'item' => [], 'i' => '__i__', 'name' => $name, 'upload' => $upload])</template>
            <button type="button" data-repeater-add class="adm-btn-light">+ Adicionar</button>
        </div>
    @elseif ($f['tr'])
        <div>
            <label class="adm-label">{{ $f['label'] }}</label>
            @foreach ($langs as $l)
                @php $v = is_array($value) ? ($value[$l->code] ?? '') : ($l->is_default ? (string) $value : ''); @endphp
                <div data-lang-field="{{ $l->code }}">
                    @if ($f['type'] === 'html')
                        <textarea data-html name="{{ $name }}[{{ $l->code }}]" rows="4" class="adm-input">{{ $v }}</textarea>
                    @elseif ($f['type'] === 'textarea')
                        <textarea name="{{ $name }}[{{ $l->code }}]" rows="3" class="adm-input">{{ $v }}</textarea>
                    @else
                        <input name="{{ $name }}[{{ $l->code }}]" value="{{ $v }}" class="adm-input">
                    @endif
                </div>
            @endforeach
        </div>
    @elseif (in_array($f['type'], ['image', 'file'], true))
        <div data-media>
            <label class="adm-label">{{ $f['label'] }}</label>
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @if ($value)
                @if ($f['type'] === 'image')
                    <img data-preview src="{{ media($value) }}" alt="" class="mb-2 max-h-28 rounded border border-slate-200 bg-slate-50">
                @else
                    <a data-preview href="{{ media($value) }}" target="_blank" class="mb-2 block text-sm text-emerald-700 underline">{{ basename($value) }}</a>
                @endif
                <button type="button" data-clear-media class="adm-btn-danger mb-2">Remover</button>
            @endif
            <input type="file" name="{{ $upload }}" accept="{{ $f['type'] === 'image' ? 'image/jpeg,image/png,image/gif,image/webp,.ico' : 'application/pdf,image/jpeg,image/png,image/gif,image/webp' }}" class="adm-input">
        </div>
    @elseif ($f['type'] === 'toggle')
        <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
            <input type="checkbox" name="{{ $name }}" value="1" @checked($value) class="size-4 accent-emerald-700"> {{ $f['label'] }}
        </label>
    @elseif ($f['type'] === 'color')
        <div><label class="adm-label">{{ $f['label'] }}</label><input type="color" name="{{ $name }}" value="{{ $value ?: '#000000' }}" class="h-10 w-20 rounded border border-slate-300"></div>
    @else
        <div><label class="adm-label">{{ $f['label'] }}</label><input name="{{ $name }}" value="{{ $value }}" class="adm-input"></div>
    @endif
@endforeach
