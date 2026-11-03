@extends('layouts.site', ['title' => $heading])

@section('content')
    <div class="section-gap pt-[45px]">
        <div class="ctn text-center">
            <div class="mb-[30px]"><h2 class="title-bar text-center">{{ $heading }}</h2></div>
            <p class="mb-[30px]">{{ $text }}</p>
            @isset($token)
                <form method="post" action="{{ route('site.newsletter.unsubscribe', $token) }}">
                    @csrf
                    <button class="btn-theme cursor-pointer">{{ __('site.newsletter_cancel_button') }}</button>
                </form>
            @else
                <a href="{{ App\Support\Site::url('') }}" class="btn-theme">{{ __('site.home') }}</a>
            @endisset
        </div>
    </div>
@endsection
