<!DOCTYPE html>
<html lang="{{ $locale_ }}">
<body style="font-family:Arial,sans-serif;color:#333;line-height:1.6;">
    <h2 style="color:#1c4e1f;">{{ $siteName }}</h2>
    <div>{!! $body !!}</div>
    <hr style="border:0;border-top:1px solid #ddd;margin:24px 0;">
    <p style="font-size:13px;color:#777;">{{ __('site.newsletter_footer', ['name' => $siteName], $locale_) }} <a href="{{ $unsubscribeUrl }}">{{ __('site.newsletter_cancel_link', [], $locale_) }}</a></p>
</body>
</html>
