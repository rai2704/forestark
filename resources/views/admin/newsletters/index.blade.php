@extends('layouts.admin')
@section('title', 'Newsletter')
@section('content')
<div class="admin-header mb-4">
    <h2><i class="fas fa-paper-plane me-2"></i>Newsletter</h2>
    @if (auth()->user()->canAccess('newsletters', 'incluir'))<a href="{{ route('admin.newsletters.create') }}" class="btn-moc btn btn-sm"><i class="fas fa-plus me-1"></i>Nova</a>@endif
</div>
@unless ($canSend)
    <div class="alert alert-warning"><i class="fas fa-triangle-exclamation me-2"></i>O envio de e-mail (SMTP) ainda não está configurado no servidor. Você pode escrever rascunhos, mas o disparo só funciona depois disso.</div>
@endunless
<p class="text-muted small"><i class="fas fa-users me-1"></i>{{ $confirmed }} inscrito(s) confirmado(s) receberão o envio, cada um no seu idioma.</p>
<div class="card shadow-sm mb-3">
    <div class="card-header admin-card-header"><i class="fas fa-list me-2"></i>Listagem</div>
    <div class="table-responsive">
        <table class="table moc-table mb-0">
            <thead><tr><th>Assunto</th><th>Status</th><th>Envios</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
                @forelse ($newsletters as $n)
                    <tr>
                        <td>{{ $n->content()['subject'] ?: '(sem assunto)' }}</td>
                        <td><span class="badge {{ $n->sent_at ? 'bg-success' : 'bg-secondary' }}">{{ $n->sent_at ? 'Enviada em '.$n->sent_at->format('d/m/Y H:i') : 'Rascunho' }}</span></td>
                        <td>{{ $n->sent_at ? $n->sent_count.' ok, '.$n->failed_count.' falha(s)' : '-' }}</td>
                        <td class="text-end text-nowrap">
                            @if (! $n->sent_at && auth()->user()->canAccess('newsletters', 'editar'))
                                <form method="post" action="{{ route('admin.newsletters.send', $n) }}" data-confirm="Enviar agora para {{ $confirmed }} inscrito(s)? Esta ação não pode ser desfeita." class="d-inline">@csrf<button class="btn-action btn-action-view" title="Enviar"><i class="fas fa-paper-plane"></i></button></form>
                                <a href="{{ route('admin.newsletters.edit', $n) }}" class="btn-action btn-action-edit" title="Editar"><i class="fas fa-pen"></i></a>
                            @endif
                            @if (auth()->user()->canAccess('newsletters', 'excluir'))
                            <form method="post" action="{{ route('admin.newsletters.destroy', $n) }}" data-confirm="Excluir esta newsletter?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Excluir"><i class="fas fa-trash"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Nenhum registro.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $newsletters->links('pagination::bootstrap-5') }}
@endsection
