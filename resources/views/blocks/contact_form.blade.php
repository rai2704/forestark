<div class="section-gap pt-[45px]">
    <div class="ctn">
        <div class="mb-[15px]"><h2 class="relative pb-6 text-center">{{ $c['title'] }}</h2></div>
        @if (session('contact_ok'))
            <p role="status" class="mb-[30px] rounded-[10px] bg-brand px-5 py-3 text-center text-white">{{ session('contact_ok') }}</p>
        @endif
        @if ($errors->hasAny(['name', 'phone', 'subject', 'other', 'message']))
            <ul role="alert" class="mb-[30px] rounded-[10px] border border-red-300 bg-red-50 px-5 py-3 text-red-800">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        @endif
        <form action="{{ route('site.contact') }}" method="post">
            @csrf
            <input type="hidden" name="locale" value="{{ app()->getLocale() }}">
            <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" class="absolute -left-[9999px]">
            <div class="grid grid-cols-1 gap-x-[30px] md:grid-cols-2 lg:grid-cols-3">
                <input type="text" name="name" required maxlength="150" value="{{ old('name') }}" placeholder="{{ $c['name'] }}" aria-label="{{ $c['name'] }}" class="field-theme h-[50px] px-5">
                <input type="email" name="email" required maxlength="190" value="{{ old('email') }}" placeholder="{{ $c['email'] }}" aria-label="{{ $c['email'] }}" class="field-theme h-[50px] px-5">
                <input type="text" name="phone" maxlength="40" value="{{ old('phone') }}" placeholder="{{ $c['phone'] }}" aria-label="{{ $c['phone'] }}" class="field-theme h-[50px] px-5 md:max-lg:col-span-2">
            </div>
            <div class="grid grid-cols-1 gap-x-[30px] lg:grid-cols-3">
                <div>
                    <select name="subject" aria-label="{{ $c['other'] }}" class="field-theme h-[50px] px-5 text-[16px] text-[#495057]">
                        @foreach ($c['items'] as $item)
                            <option @selected(old('subject') === $item['label'])>{{ $item['label'] }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="other" maxlength="150" value="{{ old('other') }}" placeholder="{{ $c['other'] }}" aria-label="{{ $c['other'] }}" class="field-theme h-[50px] px-5">
                </div>
                <textarea name="message" required maxlength="5000" placeholder="{{ $c['message'] }}" aria-label="{{ $c['message'] }}" class="field-theme h-[130px] max-h-[130px] p-5 lg:col-span-2">{{ old('message') }}</textarea>
            </div>
            <div class="pt-1.5 text-center"><button class="btn-theme">{{ $c['button'] }}</button></div>
        </form>
    </div>
</div>
