@extends('layouts.admin')
@section('title', 'Mensagem')
@section('content')
<div class="admin-header mb-4">
    <h2><i class="fas fa-envelope-open me-2"></i>Mensagem</h2>
    <a href="{{ route('admin.messages.index') }}" class="btn-moc-outline btn btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
</div>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <p class="mb-1"><b>Nome:</b> {{ $message->name }}</p>
        <p class="mb-1"><b>E-mail:</b> <a href="mailto:{{ $message->email }}">{{ $message->email }}</a></p>
        <p class="mb-1"><b>Telefone:</b> {{ $message->phone }}</p>
        <p class="mb-3"><b>Assunto:</b> {{ $message->subject }}</p>
        <p class="border-top pt-3 mb-0" style="white-space:pre-line;">{{ $message->message }}</p>
    </div>
</div>
@if (auth()->user()->canAccess('messages', 'excluir'))<form method="post" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Excluir esta mensagem?">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash me-1"></i>Excluir</button></form>@endif
@endsection
