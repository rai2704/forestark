<span role="group" aria-label="{{ __('site.accessibility') }}" class="inline-flex items-center gap-1 align-middle">
    <button type="button" data-a11y-font="-1" title="{{ __('site.font_down') }}" aria-label="{{ __('site.font_down') }}" class="cursor-pointer px-1 transition hover:text-brand hover:text-emerald-700">A-</button>
    <button type="button" data-a11y-font="0" title="{{ __('site.font_reset') }}" aria-label="{{ __('site.font_reset') }}" class="cursor-pointer px-1 transition hover:text-brand hover:text-emerald-700">A</button>
    <button type="button" data-a11y-font="1" title="{{ __('site.font_up') }}" aria-label="{{ __('site.font_up') }}" class="cursor-pointer px-1 font-bold transition hover:text-brand hover:text-emerald-700">A+</button>
    <button type="button" data-a11y-contrast aria-pressed="false" title="{{ __('site.contrast') }}" aria-label="{{ __('site.contrast') }}" class="cursor-pointer px-1 transition hover:text-brand hover:text-emerald-700">{!! $contrastIcon ?? '<span aria-hidden="true">◐</span>' !!}</button>
</span>
