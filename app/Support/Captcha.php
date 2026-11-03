<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

/** Usa o Cloudflare Turnstile quando há chaves; sem chaves, usa uma conta simples guardada na sessão. */
class Captcha
{
    public static function usesTurnstile(): bool
    {
        return (bool) (config('services.turnstile.site_key') && config('services.turnstile.secret_key'));
    }

    /** Pergunta do formulário; a mesma até ser respondida, para não invalidar outra aba aberta. */
    public static function question(string $form): string
    {
        $current = session("captcha.$form");
        if ($current) {
            return $current['q'];
        }
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        session(["captcha.$form" => ['q' => "$a + $b", 'a' => $a + $b]]);

        return "$a + $b";
    }

    public static function verify(Request $request, string $form): void
    {
        if (self::usesTurnstile()) {
            self::verifyTurnstile($request);

            return;
        }

        $current = session()->pull("captcha.$form");
        if (! $current || ! is_numeric($request->input('captcha')) || (int) $request->input('captcha') !== $current['a']) {
            throw ValidationException::withMessages(['captcha' => __('site.captcha_wrong')]);
        }
    }

    private static function verifyTurnstile(Request $request): void
    {
        $token = (string) $request->input('cf-turnstile-response');
        $passed = false;

        if ($token !== '') {
            try {
                $passed = (bool) Http::asForm()->timeout(10)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => config('services.turnstile.secret_key'),
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ])->json('success');
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if (! $passed) {
            throw ValidationException::withMessages(['captcha' => __('site.captcha_wrong')]);
        }
    }
}
