{{-- Formulário gerado pelo esquema. $fields, $data, $prefix (data[...]), $uploads (uploads[...]) --}}
@php $langs = App\Models\Language::orderBy('position')->get(); @endphp
@foreach ($fields as $f)
    @php
        $name = $prefix.'['.$f['name'].']';
        $upload = $uploads.'['.$f['name'].']';
        $value = $data[$f['name']] ?? null;
    @endphp

    @if ($f['tr'])
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
            @php
                $size = $f['size'] ?? null;
                $currentSize = $f['type'] === 'image' && $value && is_file(public_path($value)) ? @getimagesize(public_path($value)) : null;
            @endphp
            <input type="file" name="{{ $upload }}" accept="{{ $f['type'] === 'image' ? 'image/jpeg,image/png,image/gif,image/webp,.ico' : 'application/pdf,image/jpeg,image/png,image/gif,image/webp' }}" class="form-control" @if ($size) data-ideal-w="{{ $size[0] }}" data-ideal-h="{{ $size[1] }}" @endif>
            @if ($size)
                <div class="form-text" data-size-hint>
                    <strong><i class="fas fa-ruler-combined me-1"></i>Tamanho ideal: {{ $size[0] }} × {{ $size[1] }} px.</strong>
                    {{ $f['note'] ?? '' }} JPG, PNG, GIF ou WEBP, até <strong>2 MB</strong>.
                    @if ($currentSize)
                        <span @class(['d-block', 'text-danger' => $currentSize[0] < $size[0] || $currentSize[1] < $size[1]])>Imagem atual: {{ $currentSize[0] }} × {{ $currentSize[1] }} px{{ $currentSize[0] < $size[0] || $currentSize[1] < $size[1] ? ' (menor que o ideal, pode perder nitidez)' : '' }}.</span>
                    @endif
                </div>
                <div class="form-text fw-semibold" data-size-check></div>
            @endif
            @if ($f['type'] === 'file')
                <div class="form-text">PDF de até 20 MB ou imagem de até 2 MB.</div>
            @endif
        </div>
    @elseif ($f['type'] === 'toggle')
        <div class="form-check form-switch mb-3">
            <input type="checkbox" name="{{ $name }}" value="1" id="f-{{ md5($name) }}" @checked($value) class="form-check-input">
            <label class="form-check-label" for="f-{{ md5($name) }}">{{ $f['label'] }}</label>
        </div>
    @elseif ($f['type'] === 'color')
        <div class="mb-3"><label class="form-label">{{ $f['label'] }}</label><input type="color" name="{{ $name }}" value="{{ $value ?: '#000000' }}" class="form-control form-control-color"></div>
    @elseif ($f['type'] === 'video')
        <div class="mb-3">
            <label class="form-label">{{ $f['label'] }} <span class="text-danger">*</span></label>
            <input type="url" name="{{ $name }}" value="{{ $value }}" required class="form-control" placeholder="https://www.youtube.com/watch?v=...">
            <div class="form-text"><i class="fas fa-link me-1"></i>{{ $f['note'] ?? '' }}</div>
        </div>
    @else
        <div class="mb-3"><label class="form-label">{{ $f['label'] }}</label><input name="{{ $name }}" value="{{ $value }}" class="form-control"></div>
    @endif
@endforeach
