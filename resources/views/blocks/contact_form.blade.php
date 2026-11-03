@php
    use App\Support\Captcha;
    use App\Support\Site;
    $site = Site::settings();
    $input = 'w-full rounded-md border border-[#ced4da] bg-white px-3 py-2 text-[15px] text-[#333] outline-none transition focus:border-brand';
    $label = 'mb-1.5 block text-[14px] font-semibold text-[#333]';
    $required = '<span class="text-red-600" aria-hidden="true">*</span>';
    $turnstile = Captcha::usesTurnstile();
    $phone = preg_replace('/[^\d+]/', '', (string) $site['phone']);
    $whats = $site['whatsapp'] ? 'https://wa.me/'.preg_replace('/\D/', '', $site['whatsapp']) : null;
@endphp
<div class="bg-footer px-0 pt-9 pb-12 text-white" style="background:linear-gradient(135deg,var(--color-footer) 0%,var(--color-header) 100%)">
    <div class="ctn">
        <div class="mb-1 flex items-center gap-2">
            <i class="fa fa-envelope text-[22px]"></i>
            <h1 class="text-[26px] leading-tight font-bold text-white">{{ $c['title'] }}</h1>
        </div>
        <p class="opacity-85">{{ __('site.contact_intro', ['name' => $site['site_name']]) }}</p>
    </div>
</div>

<div class="section-gap pt-8">
    <div class="ctn">
        <nav aria-label="breadcrumb" class="mb-6 text-[14px]">
            <a href="{{ Site::url('') }}" class="text-brand hover:underline">{{ __('site.home') }}</a>
            <span class="mx-1.5 text-[#999]">/</span>
            <span aria-current="page">{{ $c['title'] }}</span>
        </nav>

        @if (session('contact_ok'))
            <p role="status" class="mb-6 rounded-md border border-green-300 bg-green-50 px-5 py-3 text-green-900">{{ session('contact_ok') }}</p>
        @endif
        @if ($errors->any())
            <ul role="alert" class="mb-6 rounded-md border border-red-300 bg-red-50 px-5 py-3 text-red-800">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <section class="overflow-hidden rounded-xl bg-white shadow-[0_4px_20px_rgba(0,0,0,.08)] lg:col-span-7">
                <h2 class="bg-footer px-5 py-3 font-sans text-[16px] leading-normal font-semibold text-white"><i class="fa fa-send mr-2"></i>{{ __('site.contact_send') }}</h2>
                <form action="{{ route('site.contact') }}" method="post" class="p-5">
                    @csrf
                    <input type="hidden" name="locale" value="{{ app()->getLocale() }}">
                    <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" class="absolute -left-[9999px]">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div><label for="c-name" class="{{ $label }}">{{ $c['name'] }} {!! $required !!}</label><input id="c-name" type="text" name="name" required maxlength="150" value="{{ old('name') }}" class="{{ $input }}"></div>
                        <div><label for="c-email" class="{{ $label }}">{{ $c['email'] }} {!! $required !!}</label><input id="c-email" type="email" name="email" required maxlength="190" value="{{ old('email') }}" class="{{ $input }}"></div>
                        <div><label for="c-phone" class="{{ $label }}">{{ $c['phone'] }}</label><input id="c-phone" type="text" name="phone" maxlength="40" value="{{ old('phone') }}" class="{{ $input }}"></div>
                        <div>
                            <label for="c-subject" class="{{ $label }}">{{ __('site.contact_subject') }}</label>
                            <select id="c-subject" name="subject" class="{{ $input }}">
                                @foreach ($c['items'] as $item)
                                    <option @selected(old('subject') === $item['label'])>{{ $item['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2"><label for="c-other" class="{{ $label }}">{{ $c['other'] }}</label><input id="c-other" type="text" name="other" maxlength="150" value="{{ old('other') }}" class="{{ $input }}"></div>
                        <div class="md:col-span-2"><label for="c-message" class="{{ $label }}">{{ $c['message'] }} {!! $required !!}</label><textarea id="c-message" name="message" rows="5" required maxlength="5000" class="{{ $input }}">{{ old('message') }}</textarea></div>
                        <div class="md:col-span-2">
                            @if ($turnstile)
                                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-language="{{ app()->getLocale() }}"></div>
                                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                            @else
                                <label for="c-captcha" class="{{ $label }}">{{ __('site.captcha_question', ['q' => Captcha::question()]) }} {!! $required !!}</label>
                                <input id="c-captcha" type="text" inputmode="numeric" name="captcha" required maxlength="3" autocomplete="off" class="{{ $input }} max-w-[120px]">
                            @endif
                        </div>
                        <div class="md:col-span-2"><button class="btn-theme cursor-pointer">{{ $c['button'] }}</button></div>
                    </div>
                </form>
            </section>

            <aside class="overflow-hidden rounded-xl bg-white shadow-[0_4px_20px_rgba(0,0,0,.08)] lg:col-span-5">
                <h2 class="bg-footer px-5 py-3 font-sans text-[16px] leading-normal font-semibold text-white"><i class="fa fa-info mr-2"></i>{{ __('site.contact_info') }}</h2>
                <div class="h-full bg-soft p-5">
                    <p class="mb-1 font-bold text-footer"><i class="fa fa-marker mr-1.5"></i>{{ $site['city'] }}</p>
                    <p class="mb-4 pl-4 text-[14px]">{{ $site['address'] }}</p>
                    <hr class="my-3 border-[#ddd]">
                    <ul class="space-y-2">
                        @if ($site['phone'])
                            <li><i class="fa fa-phone mr-2 text-footer"></i><a href="tel:{{ $phone }}" class="hover:text-brand">{{ $site['phone'] }}</a></li>
                        @endif
                        @if ($whats)
                            <li><i class="fa fa-whatsapp mr-2 text-[#25d366]"></i><a href="{{ $whats }}" target="_blank" rel="noopener" class="hover:text-brand">WhatsApp</a></li>
                        @endif
                        @if ($site['email'])
                            <li><i class="fa fa-envelope mr-2 text-footer"></i><a href="mailto:{{ $site['email'] }}" class="break-all hover:text-brand">{{ $site['email'] }}</a></li>
                        @endif
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</div>
