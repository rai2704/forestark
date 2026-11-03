<div data-wow class="section-gap bg-cover bg-center bg-no-repeat" style="background-image:url('{{ media($c['background']) }}')">
    <div class="flow-root max-w-[1910px] py-20 max-sm:px-[15px] lg:py-[150px] lg:pr-[14%] lg:pl-[15px]">
        <div data-carousel class="relative mx-auto max-w-[550px] rounded-[10px] bg-white/[.87] px-[15px] pt-[35px] pb-[45px] text-center [--g:0px] [--n:1] sm:px-10 lg:float-right lg:w-[550px]">
            <div class="overflow-hidden">
                <div data-track class="flex">
                    @foreach ($c['items'] as $item)
                        <div class="carousel-item">
                            <i class="flaticon flaticon-chat text-[42px] text-brand"></i>
                            <div class="pt-1 pb-[22px] text-[18px] [&_p]:leading-[30px]">{!! $item['text'] !!}</div>
                            <h6 class="text-[22px]">{{ $item['name'] }}</h6>
                            <span class="text-[13px] text-brand">{{ $item['role'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @if (count($c['items']) > 1)
                <button type="button" data-prev aria-label="Anterior" class="absolute top-1/2 -left-[125px] z-[1] size-[52px] -translate-y-1/2 rounded-full border border-white text-center text-[18px] text-white transition hover:bg-white hover:text-[#333] max-md:hidden"><i class="fa fa-left"></i></button>
                <button type="button" data-next aria-label="Próximo" class="absolute top-1/2 -right-[125px] z-[1] size-[52px] -translate-y-1/2 rounded-full border border-white text-center text-[18px] text-white transition hover:bg-white hover:text-[#333] max-md:hidden"><i class="fa fa-right"></i></button>
            @endif
        </div>
    </div>
</div>
