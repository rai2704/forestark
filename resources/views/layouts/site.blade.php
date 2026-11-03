@php
    use App\Models\MenuItem;
    use App\Support\Site;
    $site = Site::settings();
    $slug = $slug ?? '';
    $menu = fn ($location) => MenuItem::where('location', $location)->where('is_visible', true)->orderBy('position')->get()->map->content();
    $languages = Site::languages();
    $whats = $site['whatsapp'] ? 'https://wa.me/'.preg_replace('/\D/', '', $site['whatsapp']) : null;
    $copy = str_replace([':year', ':name'], [date('Y'), $site['site_name']], (string) $site['footer_copyright']);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($title ?? '') ? $title.' - ' : '' }}{{ $site['site_name'] }}</title>
    @if ($description ?? '')<meta name="description" content="{{ $description }}">@endif
    <link rel="icon" href="{{ media($site['favicon']) }}">
    <link rel="canonical" href="{{ Site::url($slug) }}">
    @foreach ($languages as $l)
        <link rel="alternate" hreflang="{{ $l->code }}" href="{{ Site::url($slug, $l->code) }}">
    @endforeach
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=open-sans:300,400,600,700|tinos:400,400i,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>:root{
        @foreach (['brand', 'header', 'footer', 'footer_bottom'] as $key)
            @if ($site['color_'.$key] ?? false) --color-{{ str_replace('_', '-', $key) }}: {{ $site['color_'.$key] }}; @endif
        @endforeach
    }</style>
</head>
<body class="overflow-x-hidden">
<header class="relative">
    <div class="bg-white py-2.5 text-[13px]">
        <div class="ctn md:flex md:justify-between max-md:text-center">
            <p class="leading-[25px]">{{ $site['label_email'] }}: <a href="mailto:{{ $site['email'] }}" class="transition hover:text-brand">{{ $site['email'] }}</a></p>
            <p class="leading-[25px]">
                @if ($languages->count() > 1)
                    <span class="mr-3 inline-flex gap-1.5" aria-label="Idioma">
                        @foreach ($languages as $l)
                            <a href="{{ Site::url($slug, $l->code) }}" lang="{{ $l->code }}" title="{{ $l->name }}" @class(['uppercase transition hover:text-brand', 'font-bold text-brand' => $l->code === app()->getLocale()])>{{ $l->code }}</a>
                        @endforeach
                    </span>
                @endif
                {{ $site['label_phone'] }}: <a href="tel:{{ preg_replace('/[^\d+]/', '', $site['phone']) }}" class="transition hover:text-brand">{{ $site['phone'] }}</a>
                @if ($whats)<a href="{{ $whats }}" target="_blank" rel="noopener" aria-label="WhatsApp" class="transition hover:text-brand"><i class="fa fa-whatsapp"></i></a>@endif
            </p>
        </div>
    </div>

    <div data-menu class="absolute top-full left-0 z-[9] w-full border-b border-white/15 bg-header pt-0 lg:pt-11 data-fixed:fixed data-fixed:top-0 data-fixed:z-[999] data-fixed:animate-menu-sticky data-fixed:pt-0 data-fixed:shadow-[0_13px_35px_-12px_rgba(35,35,35,.15)] group/menu">
        <div class="ctn">
            <div class="relative flow-root">
                <a href="{{ Site::url('') }}" class="absolute top-[5px] left-0 z-[99] lg:top-0"><img src="{{ media($site['logo']) }}" alt="{{ $site['site_name'] }}" class="max-w-full"></a>
                <nav class="relative z-[9] w-full lg:float-right lg:w-auto">
                    <div class="my-[25px] flow-root lg:hidden">
                        <button type="button" data-menu-toggle aria-label="Menu" class="float-right block">
                            <span class="mt-[5px] block h-0.5 w-[26px] rounded-[1px] bg-white"></span>
                            <span class="mt-[5px] block h-0.5 w-[26px] rounded-[1px] bg-white"></span>
                            <span class="mt-[5px] block h-0.5 w-[26px] rounded-[1px] bg-white"></span>
                        </button>
                    </div>
                    <ul data-menu-list class="max-lg:hidden max-lg:bg-white lg:flex">
                        @foreach ($menu('header') as $item)
                            <li><a href="{{ site_link($item['url']) }}" class="relative block text-[15px] capitalize transition-all duration-300 max-lg:w-full max-lg:border-b max-lg:border-black/5 max-lg:px-[15px] max-lg:leading-10 max-lg:text-[#777] lg:ml-6 lg:pt-[18px] lg:pb-[62px] lg:text-white lg:group-data-fixed/menu:pb-[30px] lg:before:absolute lg:before:-bottom-px lg:before:left-0 lg:before:h-px lg:before:w-full lg:before:bg-brand lg:before:content-['']">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>
<div class="h-[135px]"></div>

<main>@yield('content')</main>

<footer class="bg-footer pt-[50px] lg:pt-20">
    <div class="ctn">
        <div class="relative border-b border-[#373737] pb-[35px] max-md:hidden flow-root">
            <a href="{{ Site::url('') }}" class="absolute -top-2.5 left-1/2 z-[9] -translate-x-1/2"><img src="{{ media($site['logo']) }}" alt=""></a>
            <div class="relative float-left border-r border-[#373737] pr-5 text-right">
                <i class="flaticon flaticon-phone absolute -top-[3px] -right-[50px] text-[35px] text-brand"></i>
                <h6 class="text-[16px] text-[#777]">{{ $site['footer_call'] }}</h6>
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $site['phone']) }}" class="font-serif text-[20px] text-white transition hover:text-brand">{{ $site['phone'] }}</a>
                @if ($whats)<a href="{{ $whats }}" target="_blank" rel="noopener" aria-label="WhatsApp" class="font-serif text-[20px] text-white transition hover:text-brand"><i class="fa fa-whatsapp"></i></a>@endif
            </div>
            <div class="relative float-right border-l border-[#373737] pl-5">
                <i class="flaticon flaticon-mail absolute -top-[3px] -left-[55px] text-[35px] text-brand"></i>
                <h6 class="text-[16px] text-[#777]">{{ $site['footer_send'] }}</h6>
                <a href="mailto:{{ $site['email'] }}" class="font-serif text-[20px] text-white transition hover:text-brand">{{ $site['email'] }}</a>
            </div>
        </div>
    </div>

    <div class="pt-0 pb-5 md:pt-[45px] md:pb-[35px]">
        <div class="ctn">
            <div class="grid grid-cols-1 gap-x-[30px] sm:grid-cols-2 lg:grid-cols-12">
                <div class="mb-[45px] lg:col-span-3">
                    <h6 class="mb-5 text-[20px] font-bold text-white">{{ $site['footer_about_title'] }}</h6>
                    <p class="mb-[18px] border-b border-[#373737] pb-[18px]">{{ $site['footer_about'] }}</p>
                    <h6 class="mb-5 text-[20px] font-bold text-white">{{ $site['footer_where'] }}:</h6>
                    <p>{{ $site['address'] }}<br><span class="text-white">{{ $site['city'] }}</span></p>
                </div>
                <div class="mb-[45px] lg:col-span-2">
                    <h6 class="mb-5 text-[20px] font-bold text-white">{{ $site['footer_links'] }}</h6>
                    <ul>
                        @foreach ($menu('footer') as $item)
                            <li><a href="{{ site_link($item['url']) }}" class="mb-2.5 text-[#777] transition hover:text-brand">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="mb-[45px] lg:col-span-4">
                    <h6 class="mb-5 text-[20px] font-bold text-white">{{ $site['footer_comments'] }}</h6>
                </div>
                <div class="mb-[45px] lg:col-span-3">
                    <h6 class="mb-5 text-[20px] font-bold text-white">{{ $site['footer_subscribe'] }}</h6>
                    @if (session('subscribe_ok'))<p class="mb-3 text-white">{{ session('subscribe_ok') }}</p>@endif
                    <form action="{{ route('site.subscribe') }}" method="post" class="relative mb-5 h-12">
                        @csrf
                        <input type="hidden" name="locale" value="{{ app()->getLocale() }}">
                        <input type="email" name="email" required placeholder="{{ $site['footer_placeholder'] }}" aria-label="{{ $site['footer_placeholder'] }}" class="size-full rounded-[10px] bg-[#2d2d2d] pr-[58px] pl-[15px] text-[#777] outline-none">
                        <button aria-label="{{ $site['footer_subscribe'] }}" class="absolute inset-y-0 right-0 w-[55px] rounded-r-[10px] bg-brand text-[20px] text-white"><i class="fa fa-arrow"></i></button>
                    </form>
                    @error('email')<p class="mb-3 text-white">{{ $message }}</p>@enderror
                    <p>{{ $site['footer_subscribe_text'] }}</p>
                    @if ($whats)
                        <ul><li class="mt-[35px] mr-[5px] inline-block"><a href="{{ $whats }}" target="_blank" rel="noopener" aria-label="WhatsApp" class="block size-10 rounded-full bg-[#2d2d2d] text-center text-[13px] leading-10 text-[#777] transition hover:bg-brand hover:text-white"><i class="fa fa-whatsapp"></i></a></li></ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="bg-footer-bottom py-[22px] text-center">
        <div class="ctn"><p>{{ $copy }}</p></div>
    </div>
</footer>

<button type="button" data-top aria-label="Topo" class="fixed right-[5px] bottom-2.5 z-[99] hidden size-[25px] rounded-[3px] bg-brand text-center text-[18px] text-white"><i class="fa fa-up"></i></button>
</body>
</html>
