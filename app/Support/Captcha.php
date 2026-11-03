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

    public static function question(): string
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        session(['captcha_answer' => $a + $b]);

        return "$a + $b";
    }

    public static function verify(Request $request): void
    {
        if (self::usesTurnstile()) {
            self::verifyTurnstile($request);

            return;
        }

        $answer = session()->pull('captcha_answer');
        if ($answer === null || ! is_numeric($request->input('captcha')) || (int) $request->input('captcha') !== $answer) {
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
