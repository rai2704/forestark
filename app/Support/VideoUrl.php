<?php

namespace App\Support;

/** Vídeos ficam no YouTube ou no Vimeo: aqui só se aceita o link, nunca o arquivo. */
class VideoUrl
{
    /** Endereço do player, ou null se o link não for de YouTube ou Vimeo. */
    public static function embed(?string $url): ?string
    {
        $url = trim((string) $url);
        $parts = parse_url($url);
        if (! $parts || ! in_array($parts['scheme'] ?? '', ['http', 'https'], true)) {
            return null;
        }
        $host = preg_replace('/^(www\.|m\.)/', '', strtolower($parts['host'] ?? ''));
        $path = $parts['path'] ?? '';
        parse_str($parts['query'] ?? '', $query);

        if (in_array($host, ['youtube.com', 'youtube-nocookie.com', 'youtu.be'], true)) {
            $id = match (true) {
                $host === 'youtu.be' => ltrim($path, '/'),
                $path === '/watch' => (string) ($query['v'] ?? ''),
                (bool) preg_match('#^/(embed|shorts|live|v)/([^/]+)#', $path, $m) => $m[2],
                default => '',
            };

            return preg_match('/^[\w-]{6,20}$/', $id) ? 'https://www.youtube-nocookie.com/embed/'.$id : null;
        }

        if ($host === 'vimeo.com' || $host === 'player.vimeo.com') {
            if (! preg_match('#^/(?:video/)?(\d{6,12})(?:/([0-9a-f]{6,20}))?/?$#', $path, $m)) {
                return null;
            }
            $hash = $m[2] ?? ($query['h'] ?? '');

            return 'https://player.vimeo.com/video/'.$m[1].(preg_match('/^[0-9a-f]{6,20}$/', (string) $hash) ? '?h='.$hash : '');
        }

        return null;
    }
}
