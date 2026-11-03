@extends('layouts.site', ['title' => $c['title'], 'description' => strip_tags((string) ($c['text'] ?? '')), 'slug' => $slug])

@section('content')
    <div class="section-gap pt-[45px]">
        <div class="ctn">
            <nav aria-label="breadcrumb" class="mb-6 text-[14px]">
                <a href="{{ App\Support\Site::url('') }}" class="text-brand hover:underline">{{ __('site.home') }}</a>
                <span class="mx-1.5 text-[#999]">/</span>
                <a href="{{ App\Support\Site::url($route) }}" class="text-brand hover:underline">{{ $sectionTitle }}</a>
            </nav>
            <div class="mb-[30px]"><h1 class="title-bar text-center text-[35px] leading-[40px] font-normal">{{ $c['title'] }}</h1></div>
            @if ($c['text'] ?? false)<p class="mx-auto mb-[30px] max-w-[800px] text-center">{{ $c['text'] }}</p>@endif
            <div data-gallery class="grid grid-cols-2 gap-[15px] md:grid-cols-3 lg:grid-cols-4">
                @foreach ($entry->visiblePhotos() as $i => $photo)
                    <a href="{{ media($photo['image']) }}" data-lightbox class="group block aspect-[3/2] overflow-hidden bg-[#212121]" aria-label="{{ $c['title'] }}, {{ __('site.photo') }} {{ $i + 1 }}">
                        <img src="{{ media($photo['image']) }}" alt="{{ $c['title'] }}" loading="lazy" class="size-full object-cover transition-all duration-[400ms] group-hover:scale-110 group-hover:opacity-80">
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
