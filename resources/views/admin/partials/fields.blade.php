{{-- Formulário gerado pelo esquema. $fields, $data, $prefix (data[...]), $uploads (uploads[...]) --}}
@php $langs = App\Models\Language::orderBy('position')->get(); @endphp
@foreach ($fields as $f)
    @php
        $name = $prefix.'['.$f['name'].']';
        $upload = $uploads.'['.$f['name'].']';
        $value = $data[$f['name']] ?? null;
    @endphp

    @if ($f['type'] === 'repeater')
        <div data-repeater class="mb-3">
            <h6 class="fw-bold mt-3">{{ $f['label'] }}</h6>
            <div data-repeater-list>
                @foreach (array_values((array) $value) as $i => $item)
                    @include('admin.partials.repeater-item', ['f' => $f, 'item' => (array) $item, 'i' => $i, 'name' => $name, 'upload' => $upload])
                @endforeach
            </div>
            <template>@include('admin.partials.repeater-item', ['f' => $f, 'item' => [], 'i' => '__i__', 'name' => $name, 'upload' => $upload])</template>
            <button type="button" data-repeater-add class="btn-moc-outline btn btn-sm"><i class="fas fa-plus me-1"></i>Adicionar</button>
        </div>
    @elseif ($f['tr'])
        <div class="mb-3">
            <label class="form-label">{{ $f['label'] }}</label>
            @foreach ($langs as $l)
                @php $v = is_array($value) ? ($value[$l->code] ?? '') : ($l->is_default ? (string) $value : ''); @endphp
                <div data-lang-field="{{ $l->code }}">
                    @if ($f['type'] === 'html')
                        <textarea data-html name="{{ $name }}[{{ $l->code }}]" rows="4" class="form-control">{{ $v }}</textarea>
                    @elseif ($f['type'] === 'textarea')
                        <textarea name="{{ $name }}[{{ $l->code }}]" rows="3" class="form-control">{{ $v }}</textarea>
                    @else
                        <input name="{{ $name }}[{{ $l->code }}]" value="{{ $v }}" class="form-control">
                    @endif
                </div>
            @endforeach
        </div>
    @elseif (in_array($f['type'], ['image', 'file'], true))
        <div data-media class="mb-3">
            <label class="form-label">{{ $f['label'] }}</label>
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @if ($value)
                <div class="d-flex align-items-center gap-3 mb-2">
                    @if ($f['type'] === 'image')
                        <img data-preview src="{{ media($value) }}" alt="" class="media-preview">
                    @else
                        <a data-preview href="{{ media($value) }}" target="_blank" rel="noopener"><i class="fas fa-file-pdf me-1"></i>{{ basename($value) }}</a>
                    @endif
                    <button type="button" data-clear-media class="btn btn-sm btn-outline-danger"><i class="fas fa-trash me-1"></i>Remover</button>
                </div>
            @endif
            <input type="file" name="{{ $upload }}" accept="{{ $f['type'] === 'image' ? 'image/jpeg,image/png,image/gif,image/webp,.ico' : 'application/pdf,image/jpeg,image/png,image/gif,image/webp' }}" class="form-control">
        </div>
    @elseif ($f['type'] === 'toggle')
        <div class="form-check mb-3">
            <input type="checkbox" name="{{ $name }}" value="1" @checked($value) class="form-check-input">
            <label class="form-check-label">{{ $f['label'] }}</label>
        </div>
    @elseif ($f['type'] === 'color')
        <div class="mb-3"><label class="form-label">{{ $f['label'] }}</label><input type="color" name="{{ $name }}" value="{{ $value ?: '#000000' }}" class="form-control form-control-color"></div>
    @else
        <div class="mb-3"><label class="form-label">{{ $f['label'] }}</label><input name="{{ $name }}" value="{{ $value }}" class="form-control"></div>
    @endif
@endforeach
