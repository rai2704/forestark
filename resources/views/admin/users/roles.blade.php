@extends('layouts.admin')
@section('title', 'Perfis de Acesso')
@section('content')
@php
    $actions = config('portal.actions');
    $cell = function ($user, string $module) use ($actions) {
        if ($user->role?->full_access) {
            return ['total', 'Acesso total (nível '.$user->role->name.')'];
        }
        $granted = array_intersect_key($actions, array_filter($user->permissions[$module] ?? []));
        if (! $granted) {
            return ['nenhum', 'Sem acesso a este módulo'];
        }

        return [count($granted) === count($actions) ? 'total' : 'parcial', implode(', ', $granted)];
    };
@endphp
<div class="admin-header mb-4">
    <h2><i class="fas fa-shield-alt me-2"></i>Perfis de Acesso</h2>
    <a href="{{ route('admin.users.index') }}" class="btn-moc-outline btn btn-sm"><i class="fas fa-users-cog me-1"></i>Gerenciar Usuários</a>
</div>
<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap gap-4 small">
            <span><i class="fas fa-check-circle text-success me-1"></i>Acesso completo (ou nível com acesso total)</span>
            <span><i class="fas fa-adjust text-warning me-1"></i>Acesso parcial: passe o mouse para ver quais ações</span>
            <span><i class="fas fa-times text-muted me-1" style="opacity:.5;"></i>Sem acesso ao módulo</span>
        </div>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-table me-2"></i>Matriz de permissões: módulos × usuários</div>
    <div class="table-responsive">
        <table class="table moc-table mb-0 align-middle text-center" style="min-width:720px;">
            <thead>
                <tr>
                    <th class="text-start" style="min-width:170px;">Módulo</th>
                    @foreach ($users as $u)
                        <th style="min-width:90px;">
                            <div class="small fw-semibold">{{ $u->name }}</div>
                            <span class="badge {{ $u->role?->full_access ? 'bg-danger' : 'bg-secondary' }}" style="font-size:.62rem;">{{ $u->role?->name ?? '—' }}</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($groups as $group => $modules)
                    <tr class="table-light"><td colspan="{{ $users->count() + 1 }}" class="text-start fw-semibold small text-uppercase" style="letter-spacing:.04em;color:var(--moc-primary);">{{ $group }}</td></tr>
                    @foreach ($modules as $key => $module)
                        <tr>
                            <td class="text-start">{{ $module['label'] }}</td>
                            @foreach ($users as $u)
                                @php [$state, $detail] = $cell($u, $key); @endphp
                                <td title="{{ $detail }}">
                                    @if ($state === 'total')
                                        <i class="fas fa-check-circle text-success"></i>
                                    @elseif ($state === 'parcial')
                                        <i class="fas fa-adjust text-warning"></i>
                                    @else
                                        <i class="fas fa-times text-muted" style="opacity:.5;"></i>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
