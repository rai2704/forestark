<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmSubscriptionMail;
use App\Models\Subscriber;
use App\Support\Captcha;
use App\Support\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $this->applyLocale((string) $request->input('locale'));
        $back = redirect()->to(url()->previous().'#inscricao');

        if ($request->filled('website')) { // armadilha para robôs: finge sucesso
            return $back->with('subscribe_ok', __('site.newsletter_check'));
        }

        $validator = validator($request->only('email'), ['email' => 'required|email|max:190']);
        if ($validator->fails()) {
            return $back->withErrors($validator, 'subscribe')->withInput($request->only('email'));
        }

        try {
            Captcha::verify($request, 'subscribe');
        } catch (ValidationException $e) {
            return $back->withErrors($e->errors(), 'subscribe')->withInput($request->only('email'));
        }

        $subscriber = Subscriber::firstOrCreate(['email' => strtolower($request->input('email'))], ['locale' => app()->getLocale()]);

        // Sem SMTP não há como confirmar por e-mail: a inscrição vale direto até o envio ser configurado.
        if (! Site::canSendMail()) {
            $subscriber->confirmed_at ??= now();
            $subscriber->save();

            return $back->with('subscribe_ok', Site::settings()['msg_subscribe_ok']);
        }

        if (! $subscriber->confirmed_at) {
            try {
                Mail::to($subscriber->email)->send(new ConfirmSubscriptionMail(route('site.newsletter.confirm', $subscriber->token), Site::settings()['site_name'], app()->getLocale()));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // A resposta não revela se o e-mail já estava na lista.
        return $back->with('subscribe_ok', __('site.newsletter_check'));
    }

    public function confirm(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();
        $this->applyLocale((string) $subscriber->locale);
        $subscriber->confirmed_at ??= now();
        $subscriber->save();

        return view('site.newsletter', ['heading' => __('site.newsletter_confirmed_title'), 'text' => __('site.newsletter_confirmed')]);
    }

    public function unsubscribeForm(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();
        $this->applyLocale((string) $subscriber->locale);

        return view('site.newsletter', ['heading' => __('site.newsletter_cancel_title'), 'text' => __('site.newsletter_cancel_text', ['email' => $subscriber->email]), 'token' => $token]);
    }

    public function unsubscribe(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();
        $this->applyLocale((string) $subscriber->locale);
        $subscriber->delete();

        return view('site.newsletter', ['heading' => __('site.newsletter_cancel_title'), 'text' => __('site.newsletter_cancelled')]);
    }

    private function applyLocale(string $locale): void
    {
        app()->setLocale(Site::languages()->contains('code', $locale) ? $locale : Site::defaultLocale());
    }
}
