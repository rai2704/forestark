@extends('layouts.admin')
@section('title', 'Aparência e configurações')
@section('content')
    <form method="post" enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')
        @include('admin.partials.lang-tabs')
        @foreach ($groups as $title => $fields)
            <div class="adm-card space-y-4">
                <h2 class="font-semibold">{{ $title }}</h2>
                @include('admin.partials.fields', ['fields' => $fields, 'data' => $data, 'prefix' => 'data', 'uploads' => 'uploads'])
            </div>
        @endforeach
        <button class="adm-btn">Salvar configurações</button>
    </form>
@endsection
