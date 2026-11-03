@extends('layouts.admin')
@section('title', 'Menus')
@section('content')
<div class="admin-header mb-4"><h2><i class="fas fa-bars me-2"></i>Menus</h2></div>
@foreach ($locations as $location => $label)
    <div class="card shadow-sm mb-4">
        <div class="card-header admin-card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2"></i>{{ $label }}</span>
            @if (auth()->user()->canAccess('menus', 'incluir'))<a href="{{ route('admin.menus.create', ['location' => $location]) }}" class="btn btn-sm btn-light"><i class="fas fa-plus me-1"></i>Novo</a>@endif
        </div>
        <div class="table-responsive">
            <table class="table moc-table mb-0">
                <thead><tr><th>Rótulo</th><th>Link</th><th>Status</th><th class="text-end">Ações</th></tr></thead>
                <tbody>
                    @foreach ($items[$location] ?? [] as $item)
                        <tr>
                            <td>{{ $item->content()['label'] }}</td>
                            <td class="text-muted">{{ $item->content()['url'] }}</td>
                            <td><span class="badge {{ $item->is_visible ? 'bg-success' : 'bg-secondary' }}">{{ $item->is_visible ? 'Visível' : 'Oculto' }}</span></td>
                            <td class="text-end text-nowrap">
                                @if (auth()->user()->canAccess('menus', 'editar'))
                                <form method="post" action="{{ route('admin.menus.move', $item) }}" class="d-inline">@csrf<input type="hidden" name="direction" value="up"><button class="btn-action btn-action-view" title="Subir"><i class="fas fa-arrow-up"></i></button></form>
                                <form method="post" action="{{ route('admin.menus.move', $item) }}" class="d-inline">@csrf<input type="hidden" name="direction" value="down"><button class="btn-action btn-action-view" title="Descer"><i class="fas fa-arrow-down"></i></button></form>
                                <a href="{{ route('admin.menus.edit', $item) }}" class="btn-action btn-action-edit" title="Editar"><i class="fas fa-pen"></i></a>
                                @endif
                                @if (auth()->user()->canAccess('menus', 'excluir'))
                                <form method="post" action="{{ route('admin.menus.destroy', $item) }}" data-confirm="Excluir este item?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Excluir"><i class="fas fa-trash"></i></button></form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach
@endsection
