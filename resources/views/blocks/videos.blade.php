<div class="section-gap pt-[45px]">
    <div class="ctn">
        @if ($c['title'])<div class="mb-[45px]"><h2 class="title-bar text-center">{{ $c['title'] }}</h2></div>@endif
        @if (count($c['items']))
            <div class="grid grid-cols-1 gap-[30px] md:grid-cols-2 lg:grid-cols-3">
                @foreach ($c['items'] as $item)
                    @php $embed = App\Support\VideoUrl::embed($item['video'] ?? null); @endphp
                    @if ($embed)
                        <div>
                            <div class="mb-4 aspect-video bg-[#212121]">
                                <iframe src="{{ $embed }}" title="{{ $item['title'] }}" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen class="size-full border-0"></iframe>
                            </div>
                            <h6 class="font-sans text-[20px] leading-[26px] text-[#333]">{{ $item['title'] }}</h6>
                            @if ($item['text'])<p class="pt-2.5">{{ $item['text'] }}</p>@endif
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <p class="text-center">{{ __('site.empty') }}</p>
        @endif
    </div>
</div>
