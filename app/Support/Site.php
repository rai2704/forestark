<?php

namespace App\Support;

use App\Models\Language;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;

class Site
{
    private static ?Collection $languages = null;

    private static array $settings = [];

    public static function languages(): Collection
    {
        return self::$languages ??= Language::where('is_active', true)->orderBy('position')->get();
    }

    public static function allLanguageCodes(): array
    {
        return Language::orderBy('position')->pluck('code')->all();
    }

    public static function defaultLocale(): string
    {
        return self::languages()->firstWhere('is_default', true)?->code ?? 'pt';
    }

    public static function settings(): array
    {
        $fields = collect(config('portal.settings_groups'))->flatten(1)->all();

        return self::$settings[app()->getLocale()] ??= Content::resolve($fields, SiteSetting::current()->data ?? []);
    }

    /** Com o mailer de log (padrão sem SMTP) nenhum e-mail chega ao destinatário. */
    public static function canSendMail(): bool
    {
        return ! in_array(config('mail.default'), ['log', 'array'], true);
    }

    public static function flush(): void
    {
        self::$languages = null;
        self::$settings = [];
    }

    /** URL pública com o prefixo do idioma (o padrão não tem prefixo). */
    public static function url(string $path = '', ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $prefix = $locale === self::defaultLocale() ? '' : $locale;

        return url(trim($prefix.'/'.trim($path, '/'), '/'));
    }

    /** Separa o prefixo de idioma do caminho: ['en', 'a-empresa']. */
    public static function splitPath(string $path): array
    {
        $path = trim($path, '/');
        $first = explode('/', $path, 2)[0];
        if ($first !== '' && $first !== self::defaultLocale() && self::languages()->contains('code', $first)) {
            return [$first, trim(substr($path, strlen($first)), '/')];
        }

        return [self::defaultLocale(), $path];
    }
}
