@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0" style="color:var(--moc-primary);"><i class="fas fa-th-large me-2"></i>Dashboard</h4>
        <small class="text-muted">Bem-vindo, <strong>{{ auth()->user()->name }}</strong>!</small>
    </div>
    <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i>{{ now()->format('d/m/Y H:i') }}</small>
</div>

<div class="row g-3 mb-4">
    @foreach ($stats as $i => $stat)
        <div class="col-6 col-xl-3">
            <a href="{{ $stat['url'] }}" class="text-decoration-none">
                <div class="stat-card stat-bg-{{ $i + 1 }}">
                    <div>
                        <div class="stat-number">{{ $stat['value'] }}</div>
                        <div class="stat-label">{{ $stat['label'] }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas {{ $stat['icon'] }}"></i></div>
                </div>
            </a>
        </div>
    @endforeach
</div>

@if ($recent !== null)
    <div class="card shadow-sm">
        <div class="card-header admin-card-header"><i class="fas fa-envelope me-2"></i>Mensagens recentes</div>
        <div class="table-responsive">
            <table class="table moc-table mb-0">
                <thead><tr><th>Data</th><th>Nome</th><th>Assunto</th><th class="text-end">Ações</th></tr></thead>
                <tbody>
                    @forelse ($recent as $m)
                        <tr @class(['fw-semibold' => ! $m->read_at])>
                            <td>{{ $m->created_at->format('d/m/Y H:i') }}</td><td>{{ $m->name }}</td><td>{{ $m->subject }}</td>
                            <td class="text-end"><a href="{{ route('admin.messages.show', $m) }}" class="btn-action btn-action-view" title="Abrir"><i class="fas fa-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Nenhuma mensagem.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
