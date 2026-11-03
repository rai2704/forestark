<div>
    @foreach ($items as $item)
        <details name="accordion-{{ $id }}" class="group mb-[5px]">
            <summary class="relative block bg-white py-[17px] pr-[60px] pl-[30px] font-sans text-[16px] leading-[1.2] text-[#333]">
                {{ $item['title'] }}
                <span aria-hidden="true" class="absolute top-1/2 right-[30px] block size-[25px] -translate-y-1/2 rounded-full bg-brand text-center text-[19px] leading-[25px] text-white before:content-['+'] group-open:before:content-['-']"></span>
            </summary>
            <div class="accordion-body bg-soft px-0 pt-[25px] pb-5 text-justify sm:pr-[55px] sm:pl-[45px]">{!! $item['body'] !!}</div>
        </details>
    @endforeach
</div>
