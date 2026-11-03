@extends('layouts.admin')
@section('title', 'Contatos')
@section('content')
<div class="admin-header mb-4"><h2><i class="fas fa-envelope me-2"></i>Contatos</h2></div>
<div class="card shadow-sm mb-3">
    <div class="card-header admin-card-header"><i class="fas fa-list me-2"></i>Listagem</div>
    <div class="table-responsive">
        <table class="table moc-table mb-0">
            <thead><tr><th>Data</th><th>Nome</th><th>Assunto</th><th>Idioma</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
                @forelse ($messages as $m)
                    <tr @class(['fw-semibold' => ! $m->read_at])>
                        <td>{{ $m->created_at->format('d/m/Y H:i') }}</td><td>{{ $m->name }}</td><td>{{ $m->subject }}</td><td>{{ $m->locale }}</td>
                        <td class="text-end"><a href="{{ route('admin.messages.show', $m) }}" class="btn-action btn-action-view" title="Abrir"><i class="fas fa-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Nenhum registro.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $messages->links('pagination::bootstrap-5') }}
@endsection
