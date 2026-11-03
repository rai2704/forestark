<div data-hero class="group section-gap relative h-[500px] overflow-hidden sm:h-[600px] lg:h-[1000px]">
    @foreach ($c['items'] as $i => $slide)
        <div data-slide data-image="{{ media($slide['image']) }}" @class(['absolute inset-0 bg-cover bg-center', 'invisible' => $i > 0]) style="background-image:url('{{ media($slide['image']) }}')">
            <div class="absolute inset-0 bg-[rgba(30,30,30,.5)]"></div>
            <div class="ctn absolute top-1/2 left-1/2 z-[1] mt-[60px] -translate-x-1/2 -translate-y-1/2 text-center">
                @if ($slide['show_text'])
                    <h1 data-wow class="text-[40px] leading-[50px] text-white capitalize sm:text-[45px] sm:leading-[56px] md:text-[50px] md:leading-[60px] lg:text-[70px] lg:leading-[75px]"><span class="block font-sans text-[30px] font-bold sm:text-[35px] md:text-[40px] lg:text-[50px]">{{ $slide['title'] }}</span></h1>
                    <p data-wow style="animation-delay:.2s" class="mx-auto mt-[18px] mb-5 w-full text-white sm:mb-[35px] lg:w-[63%]">{{ $slide['text'] }}</p>
                @endif
                @if ($slide['show_button'])
                    <a data-wow style="animation-delay:.278s" href="{{ site_link($slide['link']) }}" class="btn-theme">{{ $slide['button'] }}</a>
                @endif
            </div>
        </div>
    @endforeach
    @if (count($c['items']) > 1)
        <button type="button" data-prev aria-label="Anterior" class="absolute top-1/2 left-5 z-20 -translate-y-1/2 border border-white/30 px-[13px] py-[15px] text-[12px] text-white uppercase opacity-0 transition [writing-mode:vertical-rl] rotate-180 group-hover:opacity-100 max-md:hidden">P r e v</button>
        <button type="button" data-next aria-label="Próximo" class="absolute top-1/2 right-5 z-20 -translate-y-1/2 border border-white/30 px-[13px] py-[15px] text-[12px] text-white uppercase opacity-0 transition [writing-mode:vertical-rl] group-hover:opacity-100 max-md:hidden">N e x t</button>
    @endif
</div>
