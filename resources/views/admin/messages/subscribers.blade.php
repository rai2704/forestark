@extends('layouts.admin')
@section('title', 'Inscritos')
@section('content')
<div class="admin-header mb-4"><h2><i class="fas fa-user-plus me-2"></i>Inscritos</h2></div>
<div class="card shadow-sm mb-3">
    <div class="card-header admin-card-header"><i class="fas fa-list me-2"></i>Listagem</div>
    <div class="table-responsive">
        <table class="table moc-table mb-0">
            <thead><tr><th>E-mail</th><th>Idioma</th><th>Data</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
                @forelse ($subscribers as $s)
                    <tr>
                        <td>{{ $s->email }}</td><td>{{ $s->locale }}</td><td>{{ $s->created_at->format('d/m/Y') }}</td>
                        <td class="text-end"><form method="post" action="{{ route('admin.subscribers.destroy', $s) }}" data-confirm="Remover este inscrito?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Remover"><i class="fas fa-trash"></i></button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Nenhum registro.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $subscribers->links('pagination::bootstrap-5') }}
@endsection
