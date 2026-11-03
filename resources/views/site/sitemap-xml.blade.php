{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach ($pages as $page)
@foreach ($languages as $l)
    <url>
        <loc>{{ App\Support\Site::url($page->slug, $l->code) }}</loc>
        <lastmod>{{ $page->updated_at->toDateString() }}</lastmod>
@foreach ($languages as $alt)
        <xhtml:link rel="alternate" hreflang="{{ $alt->code }}" href="{{ App\Support\Site::url($page->slug, $alt->code) }}"/>
@endforeach
    </url>
@endforeach
@endforeach
</urlset>
