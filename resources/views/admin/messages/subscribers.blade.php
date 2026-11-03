@extends('layouts.admin')
@section('title', 'Inscritos')
@section('content')
    <div class="adm-card overflow-x-auto">
        <table class="adm-table">
            <thead><tr><th>E-mail</th><th>Idioma</th><th>Data</th><th></th></tr></thead>
            <tbody>
                @forelse ($subscribers as $s)
                    <tr>
                        <td>{{ $s->email }}</td><td>{{ $s->locale }}</td><td>{{ $s->created_at->format('d/m/Y') }}</td>
                        <td class="text-right"><form method="post" action="{{ route('admin.subscribers.destroy', $s) }}" data-confirm="Remover este inscrito?">@csrf @method('DELETE')<button class="adm-btn-danger">Remover</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-slate-500">Nenhum inscrito.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $subscribers->links() }}
@endsection
