@extends('layouts.admin')
@section('title', 'Mensagem')
@section('content')
    <div class="adm-card space-y-2 text-sm">
        <p><b>Nome:</b> {{ $message->name }}</p>
        <p><b>E-mail:</b> <a class="text-emerald-700 underline" href="mailto:{{ $message->email }}">{{ $message->email }}</a></p>
        <p><b>Telefone:</b> {{ $message->phone }}</p>
        <p><b>Assunto:</b> {{ $message->subject }}</p>
        <p class="whitespace-pre-line border-t border-slate-200 pt-3">{{ $message->message }}</p>
    </div>
    <form method="post" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Excluir esta mensagem?">@csrf @method('DELETE')<button class="adm-btn-danger">Excluir</button></form>
@endsection
