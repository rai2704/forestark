<div class="section-gap pt-[45px]">
    <div class="ctn">
        @if ($c['title'])<div class="mb-[45px]"><h2 class="title-bar text-center">{{ $c['title'] }}</h2></div>@endif
        @if (count($c['items']))
            <div class="grid grid-cols-1 gap-[30px] sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($c['items'] as $item)
                    <div class="group text-center">
                        @if ($item['image'])
                            <div class="mb-5 aspect-square overflow-hidden bg-[#212121]">
                                <img src="{{ media($item['image']) }}" alt="{{ $item['title'] }}" loading="lazy" class="size-full object-cover transition-all duration-[400ms] group-hover:scale-110 group-hover:opacity-80">
                            </div>
                        @endif
                        <h6 class="font-sans text-[22px] leading-[28px] text-[#333]">{{ $item['title'] }}</h6>
                        @if ($item['role'])<p class="text-[14px] font-semibold text-brand">{{ $item['role'] }}</p>@endif
                        @if ($item['text'])<p class="pt-2.5">{{ $item['text'] }}</p>@endif
                        @if ($item['email'])<p class="pt-2.5"><a href="mailto:{{ $item['email'] }}" class="break-all text-brand hover:underline">{{ $item['email'] }}</a></p>@endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center">{{ __('site.empty') }}</p>
        @endif
    </div>
</div>
