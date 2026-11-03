@extends('layouts.site', ['title' => $slug === '' ? '' : $meta['title'], 'description' => $meta['meta_description'], 'slug' => $slug])

@section('content')
    @foreach ($blocks as $block)
        @includeIf('blocks.'.$block->type, ['c' => $block->content(), 'id' => $block->id])
    @endforeach
@endsection
