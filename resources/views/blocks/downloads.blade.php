<div class="px-[15px] py-[45px]">
    <h2 class="pb-[45px] text-center">{{ $c['title'] }}</h2>
    <div class="grid grid-cols-1 gap-x-[30px] md:grid-cols-3">
        @foreach ($c['items'] as $item)
            <div class="text-center">
                @if ($item['image'])<img src="{{ media($item['image']) }}" alt="" class="w-1/2">@endif
                <h5>{{ $item['title'] }}</h5>
                @if ($item['file'])<a href="{{ media($item['file']) }}" target="_blank" rel="noopener" class="btn-theme">{{ $item['button'] }}</a>@endif
            </div>
        @endforeach
    </div>
</div>
