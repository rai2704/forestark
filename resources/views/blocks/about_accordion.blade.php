<div class="section-gap">
    <div class="grid grid-cols-1 lg:grid-cols-2">
        <div class="bg-soft py-20 xl:pt-[130px] xl:pb-[175px] min-[1431px]:pt-[200px] min-[1431px]:pb-[210px]">
            <div class="flow-root pl-[15px]">
                <div class="mx-auto max-w-[540px] px-[15px] md:max-w-[720px] lg:float-right lg:mx-0 lg:pr-[15px] lg:pl-0 min-[1431px]:pr-[120px]">
                    <div class="pb-10 lg:pb-0">
                        <div class="mb-[30px]">
                            <h2 class="title-bar font-sans">{{ $c['title'] }}</h2>
                            <h3 class="text-center">{{ $c['subtitle'] }}</h3>
                        </div>
                        <div class="mb-[22px] [&_p]:mb-[22px]">{!! $c['intro'] !!}</div>
                        @foreach ($c['documents'] as $doc)
                            @if ($doc['file'] || $doc['url'])
                                <p class="mb-[22px]"><a href="{{ $doc['file'] ? media($doc['file']) : site_link($doc['url']) }}" target="_blank" rel="noopener" class="text-brand underline transition hover:text-black">{{ $doc['label'] }}</a></p>
                            @endif
                        @endforeach
                        @include('site.accordion', ['items' => $c['items'], 'id' => $id])
                        @if ($c['button'])
                            <a href="{{ site_link($c['link']) }}" class="btn-theme mt-[30px]">{{ $c['button'] }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="grid h-[600px] grid-cols-1 sm:h-[450px] sm:grid-cols-2 md:h-[500px] lg:h-[891px]">
            <div class="h-full bg-cover" style="background-image:url('{{ media($c['image_1']) }}')"></div>
            <div class="grid grid-rows-2">
                <div class="bg-cover" style="background-image:url('{{ media($c['image_2']) }}')"></div>
                <div class="bg-cover" style="background-image:url('{{ media($c['image_3']) }}')"></div>
            </div>
        </div>
    </div>
</div>
