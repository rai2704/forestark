<?php

use App\Support\Site;

/** URL de arquivo enviado. */
function media(?string $path): string
{
    return $path ? asset($path) : '';
}

/** Resolve um link salvo: links internos ganham o prefixo do idioma atual. */
function site_link(?string $link, ?string $locale = null): string
{
    $link = (string) $link;
    if ($link === '' || preg_match('#^(https?:|mailto:|tel:|\#)#', $link)) {
        return $link ?: '#';
    }
    if (str_starts_with(ltrim($link, '/'), 'uploads/')) {
        return asset(ltrim($link, '/'));
    }

    return Site::url($link, $locale);
}
