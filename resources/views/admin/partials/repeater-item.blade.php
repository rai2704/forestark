<div data-repeater-item class="space-y-3 rounded-lg border border-slate-200 bg-slate-50 p-4">
    <div class="flex justify-end gap-1">
        <button type="button" data-up class="adm-btn-light" aria-label="Subir">↑</button>
        <button type="button" data-down class="adm-btn-light" aria-label="Descer">↓</button>
        <button type="button" data-remove class="adm-btn-danger">Remover</button>
    </div>
    @include('admin.partials.fields', ['fields' => $f['fields'], 'data' => $item, 'prefix' => $name.'['.$i.']', 'uploads' => $upload.'['.$i.']'])
</div>
