<div class="section-gap bg-cover bg-fixed bg-center bg-no-repeat" style="background-image:url('{{ media($c['background']) }}')">
    <div class="bg-[rgba(55,100,325,.7)] py-[50px] sm:py-[90px]">
        <div class="ctn">
            @if (count($c['items']))
                <div class="flex flex-wrap items-center justify-center gap-8">
                    @foreach ($c['items'] as $item)
                        <img src="{{ media($item['image']) }}" alt="" class="max-h-24">
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
