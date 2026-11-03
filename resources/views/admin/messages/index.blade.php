@extends('layouts.admin')
@section('title', 'Mensagens')
@section('content')
    <div class="adm-card overflow-x-auto">
        <table class="adm-table">
            <thead><tr><th>Data</th><th>Nome</th><th>Assunto</th><th>Idioma</th><th></th></tr></thead>
            <tbody>
                @forelse ($messages as $m)
                    <tr @class(['font-semibold' => ! $m->read_at])>
                        <td>{{ $m->created_at->format('d/m/Y H:i') }}</td><td>{{ $m->name }}</td><td>{{ $m->subject }}</td><td>{{ $m->locale }}</td>
                        <td class="text-right"><a href="{{ route('admin.messages.show', $m) }}" class="adm-btn-light">Abrir</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-slate-500">Nenhuma mensagem.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $messages->links() }}
@endsection
