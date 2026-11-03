<div class="section-gap pt-[45px]">
    <div class="ctn">
        @if ($c['title'])<div class="mb-[45px]"><h2 class="title-bar text-center">{{ $c['title'] }}</h2></div>@endif
        @if ($c['image'] ?? false)<img src="{{ media($c['image']) }}" alt="" class="mx-auto mb-[30px] max-w-full">@endif
        <div class="[&_a]:text-brand [&_a]:underline [&_ol]:list-decimal [&_ol]:pl-6 [&_p]:mb-4 [&_ul]:list-disc [&_ul]:pl-6">{!! $c['body'] !!}</div>
    </div>
</div>
