<!DOCTYPE html>
<html lang="{{ $locale_ }}">
<body style="font-family:Arial,sans-serif;color:#333;line-height:1.6;">
    <h2 style="color:#1c4e1f;">{{ $siteName }}</h2>
    <p>{{ __('site.newsletter_confirm_body', [], $locale_) }}</p>
    <p><a href="{{ $url }}" style="display:inline-block;background:#379c3e;color:#fff;padding:12px 24px;text-decoration:none;font-weight:bold;">{{ __('site.newsletter_confirm_button', [], $locale_) }}</a></p>
    <p style="font-size:13px;color:#777;">{{ __('site.newsletter_confirm_ignore', [], $locale_) }}</p>
</body>
</html>
