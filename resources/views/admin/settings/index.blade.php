@extends('layouts.admin')
@section('title', 'Configurações')
@section('content')
<div class="admin-header mb-4"><h2><i class="fas fa-sliders-h me-2"></i>Configurações</h2></div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-list me-2"></i>Listagem</div>
    <div class="table-responsive">
        <table class="table moc-table mb-0">
            <thead><tr><th>Grupo</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
                @foreach ($groups as $group)
                    <tr>
                        <td>{{ $group }}</td>
                        <td class="text-end">
                            @if (auth()->user()->canAccess('settings', 'editar'))
                                <a href="{{ route('admin.settings.edit', Illuminate\Support\Str::slug($group)) }}" class="btn-action btn-action-edit" title="Editar"><i class="fas fa-pen"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
