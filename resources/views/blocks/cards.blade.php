<div @class(['section-gap', 'pt-[45px]' => $c['grid'] ?? false])>
    <div class="ctn">
        @if ($c['title'])
            <div class="mb-[45px]"><h2 class="title-bar text-center">{{ $c['title'] }}</h2></div>
        @endif
        <div @if (! ($c['grid'] ?? false)) data-carousel @endif class="[--g:15px] [--n:1] min-[500px]:[--n:2] lg:[--n:3]">
            <div class="overflow-hidden">
                <div data-track @class(['items-start gap-[15px]', 'grid gap-y-[45px] min-[500px]:grid-cols-2 lg:grid-cols-3' => $c['grid'] ?? false, 'flex' => ! ($c['grid'] ?? false)])>
                    @foreach ($c['items'] as $item)
                        <div @class(['group', 'carousel-item' => ! ($c['grid'] ?? false)])>
                            <div class="relative mb-[30px] overflow-hidden bg-[#212121]">
                                <img src="{{ media($item['image']) }}" alt="{{ $item['title'] ?: $item['badge'] }}" class="w-full transition-all duration-[400ms] group-hover:scale-110 group-hover:opacity-80">
                                @if ($item['badge'])
                                    <span class="absolute bottom-2.5 left-2.5 bg-brand px-5 font-serif text-[14px] leading-[30px] font-bold text-white">{{ $item['badge'] }}</span>
                                @endif
                            </div>
                            @if ($item['author'] || $item['title'] || $item['text'])
                                <div class="text-center">
                                    @if ($item['author'])
                                        <p class="text-[14px] leading-[22.5px] italic">{{ $item['by'] }} @if ($item['author_url'])<a href="{{ site_link($item['author_url']) }}" target="_blank" rel="noopener" class="transition hover:text-brand">{{ $item['author'] }}</a>@else{{ $item['author'] }}@endif</p>
                                    @endif
                                    @if ($item['title'])
                                        <h6 class="font-sans text-[22px] leading-[28px] text-[#333] transition group-hover:text-brand xl:px-[15px]">{{ $item['title'] }}@if ($item['highlight'])<br><b>{{ $item['highlight'] }}</b>@endif</h6>
                                    @endif
                                    @if ($item['text'])<p class="pt-2.5">{{ $item['text'] }}</p>@endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
