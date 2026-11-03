<div data-repeater-item class="border rounded bg-light p-3 mb-3">
    <div class="d-flex justify-content-end gap-1 mb-2">
        <button type="button" data-up class="btn btn-sm btn-outline-secondary" aria-label="Subir"><i class="fas fa-arrow-up"></i></button>
        <button type="button" data-down class="btn btn-sm btn-outline-secondary" aria-label="Descer"><i class="fas fa-arrow-down"></i></button>
        <button type="button" data-remove class="btn btn-sm btn-outline-danger"><i class="fas fa-trash me-1"></i>Remover</button>
    </div>
    @include('admin.partials.fields', ['fields' => $f['fields'], 'data' => $item, 'prefix' => $name.'['.$i.']', 'uploads' => $upload.'['.$i.']'])
</div>
