@extends('layouts.site', ['title' => __('site.sitemap'), 'slug' => $slug])

@section('content')
    <div class="section-gap pt-[45px]">
        <div class="ctn">
            <div class="mb-[45px]"><h2 class="title-bar text-center">{{ __('site.sitemap') }}</h2></div>
            <ul class="mx-auto max-w-[600px] text-[18px]">
                @foreach ($links as $link)
                    <li class="border-b border-[#e6e6e6]">
                        <a href="{{ App\Support\Site::url($link['slug']) }}" class="block py-3 transition hover:text-brand">{{ $link['title'] }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
