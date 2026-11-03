<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mews\Purifier\Facades\Purifier;

/** Lê e grava conteúdo orientado por esquema (config/portal.php). */
class Content
{
    private const IMAGE_MIMES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp', 'image/x-icon' => 'ico', 'image/vnd.microsoft.icon' => 'ico'];

    private const FILE_MIMES = ['application/pdf' => 'pdf'];

    public const MAX_IMAGE_BYTES = 2 * 1024 * 1024;

    private const MAX_FILE_BYTES = 20 * 1024 * 1024;

    /** Converte {campo: {pt, en}} no valor do idioma atual, com o idioma padrão como reserva. */
    public static function resolve(array $fields, array $data, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $default = Site::defaultLocale();
        $out = [];

        foreach ($fields as $f) {
            $value = $data[$f['name']] ?? null;
            if ($f['type'] === 'repeater') {
                $out[$f['name']] = array_map(fn ($item) => self::resolve($f['fields'], (array) $item, $locale), array_values((array) $value));
            } elseif ($f['tr']) {
                $value = is_array($value) ? $value : [$default => $value];
                $out[$f['name']] = ($value[$locale] ?? '') !== '' ? $value[$locale] : ($value[$default] ?? '');
            } else {
                $out[$f['name']] = $value;
            }
        }

        return $out;
    }

    /** Imagem e título de um item, para as listagens do admin. */
    public static function summary(array $fields, array $item): array
    {
        $resolved = self::resolve($fields, $item, Site::defaultLocale());
        $image = collect($fields)->firstWhere('type', 'image');
        $title = '';
        foreach ($fields as $f) {
            if ($f['name'] !== 'by' && $f['tr'] && in_array($f['type'], ['text', 'textarea', 'html'], true) && trim(strip_tags((string) $resolved[$f['name']])) !== '') {
                $title = trim(html_entity_decode(strip_tags((string) $resolved[$f['name']])));
                break;
            }
        }

        $title = $title ?: (string) ($resolved['author'] ?? '');
        if (! empty($resolved['highlight'])) {
            $title = trim($title.' '.$resolved['highlight']);
        }

        return ['image' => $image ? ($resolved[$image['name']] ?? null) : null, 'title' => Str::limit($title, 90)];
    }

    /** Monta o JSON a gravar a partir do formulário, tratando uploads e higienizando HTML. */
    public static function fromInput(array $fields, array $input, array $files = []): array
    {
        $out = [];
        foreach ($fields as $f) {
            $name = $f['name'];
            $value = $input[$name] ?? null;

            if ($f['type'] === 'repeater') {
                $out[$name] = [];
                foreach ((array) $value as $key => $item) {
                    $out[$name][] = self::fromInput($f['fields'], (array) $item, (array) ($files[$name][$key] ?? []));
                }
            } elseif ($f['tr']) {
                $out[$name] = [];
                foreach (Site::allLanguageCodes() as $code) {
                    $out[$name][$code] = self::clean($f['type'], $value[$code] ?? '');
                }
            } elseif (in_array($f['type'], ['image', 'file'], true)) {
                $upload = $files[$name] ?? null;
                $out[$name] = $upload instanceof UploadedFile
                    ? self::store($upload, $f['type'], $name)
                    : self::safeStoredPath((string) $value);
            } elseif ($f['type'] === 'video') {
                $out[$name] = self::video((string) $value, $f);
            } else {
                $out[$name] = self::clean($f['type'], $value);
            }
        }

        return $out;
    }

    private static function video(string $url, array $field): string
    {
        $url = trim($url);
        if ($url === '' && empty($field['required'])) {
            return '';
        }
        if (! VideoUrl::embed($url)) {
            throw ValidationException::withMessages([$field['name'] => 'Informe um link válido de vídeo do YouTube ou do Vimeo.']);
        }

        return $url;
    }

    private static function clean(string $type, mixed $value): mixed
    {
        return match ($type) {
            'toggle' => (bool) $value,
            'html' => trim((string) Purifier::clean((string) $value)),
            'url' => self::safeUrl((string) $value),
            'color' => preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value) ? $value : '',
            default => trim((string) $value),
        };
    }

    public static function safeUrl(string $url): string
    {
        $url = trim($url);

        return preg_match('#^(https?://|mailto:|tel:|/(?!/)|\#)#i', $url) || preg_match('#^[a-z0-9_-]+(/[a-z0-9_./-]*)?$#i', $url) ? $url : '';
    }

    private static function safeStoredPath(string $path): string
    {
        return str_starts_with($path, 'uploads/') && ! str_contains($path, '..') ? $path : '';
    }

    public static function assertImage(UploadedFile $file, string $field): void
    {
        if (! $file->isValid() || ! isset(self::IMAGE_MIMES[$file->getMimeType()])) {
            throw ValidationException::withMessages([$field => 'Arquivo inválido: "'.$file->getClientOriginalName().'". Envie JPG, PNG, GIF, WEBP ou ICO.']);
        }
        if ($file->getSize() > self::MAX_IMAGE_BYTES) {
            throw ValidationException::withMessages([$field => self::tooBig($file)]);
        }
    }

    private static function tooBig(UploadedFile $file): string
    {
        return 'A imagem "'.$file->getClientOriginalName().'" tem '.number_format($file->getSize() / 1048576, 1, ',', '').' MB. O limite é de 2 MB por imagem: reduza o arquivo e envie novamente.';
    }

    public static function storeImage(UploadedFile $file, string $field): string
    {
        return self::store($file, 'image', $field);
    }

    /** Aceita somente tipos permitidos, pelo conteúdo real, e grava com nome aleatório. */
    private static function store(UploadedFile $file, string $type, string $field): string
    {
        $allowed = $type === 'image' ? self::IMAGE_MIMES : self::IMAGE_MIMES + self::FILE_MIMES;
        $mime = $file->getMimeType();
        $extension = $allowed[$mime] ?? null;

        if (! $file->isValid() || ! $extension) {
            throw ValidationException::withMessages([$field => 'Arquivo inválido: "'.$file->getClientOriginalName().'". Envie JPG, PNG, GIF, WEBP, ICO ou PDF.']);
        }
        if (isset(self::IMAGE_MIMES[$mime]) && $file->getSize() > self::MAX_IMAGE_BYTES) {
            throw ValidationException::withMessages([$field => self::tooBig($file)]);
        }
        if ($file->getSize() > self::MAX_FILE_BYTES) {
            throw ValidationException::withMessages([$field => 'O arquivo "'.$file->getClientOriginalName().'" passa do limite de 20 MB.']);
        }

        $dir = 'uploads/'.date('Y/m');
        $name = bin2hex(random_bytes(16)).'.'.$extension;
        $file->move(public_path($dir), $name);

        return $dir.'/'.$name;
    }
}
