@extends('layouts.admin')
@section('title', 'Idiomas')
@section('content')
<div class="admin-header mb-4"><h2><i class="fas fa-language me-2"></i>Idiomas</h2></div>
<div class="card shadow-sm mb-4">
    <div class="card-header admin-card-header"><i class="fas fa-list me-2"></i>Listagem</div>
    <div class="table-responsive">
        <table class="table moc-table mb-0">
            <thead><tr><th>Código</th><th>Bandeira</th><th>Nome</th><th>Ordem</th><th>Ativo</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
                @foreach ($languages as $l)
                    <tr>
                        <td><code>{{ $l->code }}</code> @if ($l->is_default)<span class="badge bg-success">padrão</span>@endif</td>
                        <td><span class="d-flex align-items-center gap-2">@if ($l->flagUrl())<img src="{{ $l->flagUrl() }}" alt="" class="flag-sm">@endif<input form="lang-{{ $l->id }}" name="flag" value="{{ $l->flag }}" required pattern="[a-z]{2}" maxlength="2" aria-label="Código da bandeira" class="form-control form-control-sm" style="width:60px;"></span></td>
                        <td><input form="lang-{{ $l->id }}" name="name" value="{{ $l->name }}" required aria-label="Nome" class="form-control form-control-sm"></td>
                        <td><input form="lang-{{ $l->id }}" type="number" min="0" name="position" value="{{ $l->position }}" aria-label="Ordem" class="form-control form-control-sm" style="width:70px;"></td>
                        <td><input form="lang-{{ $l->id }}" type="checkbox" name="is_active" value="1" @checked($l->is_active) @disabled($l->is_default) aria-label="Ativo" class="form-check-input"></td>
                        <td class="text-end text-nowrap">
                            <form id="lang-{{ $l->id }}" method="post" action="{{ route('admin.languages.update', $l) }}" class="d-inline">@csrf @method('PUT')<button class="btn-action btn-action-edit" title="Salvar"><i class="fas fa-save"></i></button></form>
                            @unless ($l->is_default)
                                <form method="post" action="{{ route('admin.languages.destroy', $l) }}" data-confirm="Excluir este idioma?" class="d-inline">@csrf @method('DELETE')<button class="btn-action btn-action-del" title="Excluir"><i class="fas fa-trash"></i></button></form>
                            @endunless
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header admin-card-header"><i class="fas fa-plus me-2"></i>Novo idioma</div>
    <div class="card-body">
        <form method="post" action="{{ route('admin.languages.store') }}" class="row g-3 align-items-end admin-form">
            @csrf
            <div class="col-sm-3"><label class="form-label">Código (ex.: fr, de, it)</label><input name="code" required pattern="[a-z]{2}(-[A-Z]{2})?" class="form-control"></div>
            <div class="col-sm-3"><label class="form-label">Bandeira: código do país</label><input name="flag" required pattern="[a-z]{2}" maxlength="2" class="form-control"></div>
            <div class="col-sm-4"><label class="form-label">Nome</label><input name="name" required class="form-control"></div>
            <div class="col-sm-2"><button class="btn-moc btn w-100"><i class="fas fa-plus me-1"></i>Adicionar</button></div>
        </form>
    </div>
</div>
@endsection
