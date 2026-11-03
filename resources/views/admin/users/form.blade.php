@extends('layouts.admin')
@section('title', $user->exists ? 'Editar Usuário' : 'Novo Usuário')
@section('content')
@php
    $me = auth()->user();
    $selectable = $me->role?->full_access ? $roles : $roles->where('full_access', false);
    $selected = old('role_id', $user->role_id ?? $selectable->firstWhere('full_access', false)?->id);
    $current = old('perm', $user->permissions ?? []);
@endphp
<div class="admin-header mb-4">
    <h2><i class="fas fa-users-cog me-2"></i>{{ $user->exists ? 'Editar' : 'Novo' }} Usuário</h2>
    <a href="{{ route('admin.users.index') }}" class="btn-moc-outline btn btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
</div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-edit me-2"></i>Formulário</div>
    <div class="card-body">
        <form method="post" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="admin-form">
            @csrf
            @if ($user->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nome <span class="text-danger">*</span></label><input name="name" value="{{ old('name', $user->name) }}" required class="form-control"></div>
                <div class="col-md-6"><label class="form-label">E-mail <span class="text-danger">*</span></label><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control"></div>
                <div class="col-md-6">
                    <label class="form-label">Nível <span class="text-danger">*</span></label>
                    <select name="role_id" required class="form-select" data-role-select>
                        @foreach ($selectable as $role)
                            <option value="{{ $role->id }}" data-full="{{ $role->full_access ? 1 : 0 }}" @selected($selected == $role->id)>{{ $role->name }}{{ $role->full_access ? ' (acesso total)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end"><div class="form-check form-switch mb-2"><input type="checkbox" name="is_active" value="1" id="act" @checked(old('is_active', $user->is_active)) class="form-check-input"><label class="form-check-label" for="act">Ativo</label></div></div>
                <div class="col-md-6"><label class="form-label">Senha{{ $user->exists ? ' (deixe em branco para manter)' : '' }}</label><input type="password" name="password" minlength="10" autocomplete="new-password" @required(! $user->exists) class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Confirmar senha</label><input type="password" name="password_confirmation" autocomplete="new-password" class="form-control"></div>
            </div>

            <div class="mt-4 pt-2 border-top" data-perms data-full-ids="{{ $roles->where('full_access', true)->pluck('id')->join(',') }}">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <h6 class="fw-semibold mb-0"><i class="fas fa-shield-alt me-1" style="color:var(--moc-primary);"></i>Permissões</h6>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="toggleAllPerms" style="width:2.2em;height:1.2em;">
                        <label class="form-check-label small fw-semibold" for="toggleAllPerms" id="toggleAllLabel">Marcar todos</label>
                    </div>
                </div>
                <div class="row g-2">
                    @foreach (config('portal.modules') as $mod => $cfg)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="border rounded p-2 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <p class="fw-semibold small mb-0" style="color:var(--moc-primary);">{{ $cfg['label'] }}</p>
                                    <input type="checkbox" class="perm-mod-toggle" title="Marcar módulo" aria-label="Marcar módulo {{ $cfg['label'] }}" data-mod="{{ $mod }}" style="accent-color:var(--moc-primary);">
                                </div>
                                @foreach (config('portal.actions') as $action => $label)
                                    <div class="form-check form-switch mb-1">
                                        <input class="form-check-input perm-cb" type="checkbox" name="perm[{{ $mod }}][{{ $action }}]" id="p_{{ $mod }}_{{ $action }}" value="1" data-mod="{{ $mod }}" @checked(! empty($current[$mod][$action]))>
                                        <label class="form-check-label small" for="p_{{ $mod }}_{{ $action }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <p class="alert alert-info small mt-4 mb-0 d-none" data-perms-full><i class="fas fa-circle-info me-1"></i>Este nível tem acesso total a todos os módulos.</p>

            <div class="mt-4"><button class="btn-moc btn"><i class="fas fa-save me-1"></i>Salvar</button></div>
        </form>
    </div>
</div>
@endsection
