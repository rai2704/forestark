@extends('layouts.admin')
@section('title', 'Painel')
@section('content')
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($stats as $label => $value)
            <div class="adm-card"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-1 text-3xl font-bold">{{ $value }}</p></div>
        @endforeach
    </div>
@endsection
