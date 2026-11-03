@extends('layouts.site', ['title' => $c['title'], 'description' => strip_tags((string) ($c['text'] ?? '')), 'slug' => $slug])

@section('content')
    <div class="section-gap pt-[45px]">
        <div class="ctn">
            <nav aria-label="breadcrumb" class="mb-6 text-[14px]">
                <a href="{{ App\Support\Site::url('') }}" class="text-brand hover:underline">{{ __('site.home') }}</a>
                <span class="mx-1.5 text-[#999]">/</span>
                <a href="{{ App\Support\Site::url($route) }}" class="text-brand hover:underline">{{ $sectionTitle }}</a>
            </nav>
            <div class="mx-auto max-w-[900px]">
                <div class="mb-[30px]"><h1 class="title-bar text-[35px] leading-[40px] font-normal">{{ $c['title'] }}@if ($c['highlight'] ?? false)<br><b>{{ $c['highlight'] }}</b>@endif</h1></div>
                <p class="mb-5 font-serif text-[14px] italic">
                    @if ($entry->published_at){{ $entry->published_at->format('d/m/Y') }}@endif
                    @if (($c['author'] ?? false)) &nbsp;·&nbsp; {{ $c['by'] ?? '' }} {{ $c['author'] }}@endif
                </p>
                @if ($c['image'] ?? false)<img src="{{ media($c['image']) }}" alt="{{ $c['title'] }}" class="mb-[30px] w-full">@endif
                <div class="[&_a]:text-brand [&_a]:underline [&_ol]:list-decimal [&_ol]:pl-6 [&_p]:mb-4 [&_ul]:list-disc [&_ul]:pl-6">{!! $c['body'] !!}</div>
                @if ($c['file'] ?? false)
                    <p class="mt-6"><a href="{{ media($c['file']) }}" target="_blank" rel="noopener" class="btn-theme">{{ $c['button'] ?: __('site.home') }}</a></p>
                @endif
            </div>
        </div>
    </div>
@endsection
