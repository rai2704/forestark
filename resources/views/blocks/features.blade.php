<div class="px-[15px] pt-[45px]">
    <div class="section-gap">
        <div class="ctn">
            <div data-carousel class="[--g:30px] [--n:1] min-[575px]:[--n:2] lg:[--n:3]">
                <div class="overflow-hidden">
                    <div data-track class="flex items-start gap-[30px]">
                        @foreach ($c['items'] as $item)
                            <div class="carousel-item">
                                <h5 class="relative mb-5 pb-[22px] before:absolute before:bottom-0 before:left-0 before:h-px before:w-[23px] before:bg-brand before:content-['']">{{ $item['title'] }}</h5>
                                <p class="xl:pr-[60px]">{{ $item['text'] }}</p>
                                @if ($item['button'])
                                    <a href="{{ site_link($item['link']) }}" class="mt-[25px] text-brand capitalize underline transition hover:text-black">{{ $item['button'] }}</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
