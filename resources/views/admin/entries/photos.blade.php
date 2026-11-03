@php
    $photos = array_values((array) ($entry->data['photos'] ?? []));
    $canEdit = auth()->user()->canAccess($collection, 'editar');
    $last = count($photos) - 1;
@endphp
<div class="card shadow-sm mt-4" data-photos>
    <div class="card-header admin-card-header"><i class="fas fa-images me-2"></i>Fotos do álbum ({{ count($photos) }})</div>
    <div class="card-body">
        @if ($canEdit)
            <form method="post" action="{{ route('admin.entries.photos.store', [$collection, $entry]) }}" enctype="multipart/form-data" class="row g-2 align-items-start mb-3 pb-3 border-bottom">
                @csrf
                <div class="col-lg-8">
                    <input type="file" name="photos[]" multiple required accept="image/jpeg,image/png,image/webp" class="form-control" aria-label="Fotos">
                    <div class="form-text"><strong><i class="fas fa-ruler-combined me-1"></i>Tamanho ideal: 1600 × 1067 px.</strong> Selecione várias de uma vez. JPG, PNG ou WEBP, até <strong>2 MB</strong> cada, no máximo 20 por envio.</div>
                </div>
                <div class="col-auto"><button class="btn-moc btn"><i class="fas fa-upload me-1"></i>Enviar fotos</button></div>
            </form>
        @endif
        <div class="row g-3">
            @forelse ($photos as $i => $photo)
                <div class="col-6 col-md-4 col-xl-3" data-photo>
                    <div class="border rounded p-2 h-100 {{ empty($photo['hidden']) ? '' : 'bg-light' }}">
                        <img src="{{ media($photo['image']) }}" alt="" class="w-100 rounded mb-2" style="height:130px;object-fit:cover;{{ empty($photo['hidden']) ? '' : 'opacity:.45;' }}">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                            <span class="badge {{ empty($photo['hidden']) ? 'bg-success' : 'bg-secondary' }}">{{ empty($photo['hidden']) ? 'Publicada' : 'Oculta' }}</span>
                            @if ($canEdit)
                                <span class="text-nowrap">
                                    @if ($i > 0)
                                        <form method="post" action="{{ route('admin.entries.photos.move', [$collection, $entry, $i]) }}" class="d-inline">@csrf<input type="hidden" name="direction" value="up"><button class="btn-action btn-action-view" title="Mover para antes"><i class="fas fa-arrow-left"></i></button></form>
                                    @endif
                                    @if ($i < $last)
                                        <form method="post" action="{{ route('admin.entries.photos.move', [$collection, $entry, $i]) }}" class="d-inline">@csrf<input type="hidden" name="direction" value="down"><button class="btn-action btn-action-view" title="Mover para depois"><i class="fas fa-arrow-right"></i></button></form>
                                    @endif
                                    <form method="post" action="{{ route('admin.entries.photos.toggle', [$collection, $entry, $i]) }}" class="d-inline">@csrf<button class="btn-action btn-action-view" title="{{ empty($photo['hidden']) ? 'Ocultar' : 'Publicar' }}"><i class="fas {{ empty($photo['hidden']) ? 'fa-eye-slash' : 'fa-eye' }}"></i></button></form>
                                    <form method="post" action="{{ route('admin.entries.photos.destroy', [$collection, $entry, $i]) }}" data-confirm="Excluir esta foto?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Excluir"><i class="fas fa-trash"></i></button></form>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">Nenhuma foto ainda. O álbum só aparece no site depois de ter fotos publicadas.</p>
            @endforelse
        </div>
    </div>
</div>
