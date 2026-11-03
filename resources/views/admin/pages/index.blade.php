@extends('layouts.admin')
@section('title', 'Páginas')
@section('content')
    <a href="{{ route('admin.pages.create') }}" class="adm-btn">+ Nova página</a>
    <div class="adm-card overflow-x-auto">
        <table class="adm-table">
            <thead><tr><th>Título</th><th>Endereço</th><th>Blocos</th><th>Situação</th><th></th></tr></thead>
            <tbody>
                @foreach ($pages as $page)
                    <tr>
                        <td>{{ $page->content()['title'] }}</td>
                        <td class="text-slate-500">/{{ $page->slug }}</td>
                        <td>{{ $page->blocks_count }}</td>
                        <td>{{ $page->is_published ? 'Publicada' : 'Rascunho' }}</td>
                        <td class="text-right"><a href="{{ route('admin.pages.edit', $page) }}" class="adm-btn-light">Editar</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
