@php $site = App\Support\Site::settings(); @endphp
@extends('layouts.site', ['title' => '404'])

@section('content')
    <div class="section-gap text-center">
        <div class="ctn">
            <h2 class="text-[160px] font-bold leading-[1.2] tracking-[2px] sm:text-[270px]">404</h2>
            <h3 class="-mt-[30px] text-[40px] font-normal sm:-mt-20">{{ $site['msg_404_title'] }}</h3>
            <p class="mb-[22px]">{{ $site['msg_404_text'] }}</p>
            <a href="{{ App\Support\Site::url('') }}" class="btn-theme">{{ $site['msg_404_button'] }}</a>
        </div>
    </div>
@endsection
