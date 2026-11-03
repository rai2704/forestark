<div class="section-gap relative bg-soft py-[90px] lg:pt-[135px] lg:pb-[150px]">
    @if ($c['image'])
        <img data-wow style="animation-delay:.3s" src="{{ media($c['image']) }}" alt="" class="absolute bottom-0 left-0 z-[1] max-lg:hidden">
    @endif
    <div class="ctn flow-root">
        <div class="relative z-[9] w-full lg:float-right lg:w-[70%] xl:w-[54%]">
            <h2 class="mb-[25px] bg-white text-center">{{ $c['title'] }}</h2>
            @include('site.accordion', ['items' => $c['items'], 'id' => $id])
        </div>
    </div>
</div>
