<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Subscriber;
use App\Support\Captcha;
use App\Support\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SiteController extends Controller
{
    public function show(string $path = '')
    {
        [$locale, $slug] = Site::splitPath($path);
        app()->setLocale($locale);

        if ($slug === 'mapa-do-site') {
            return view('site.sitemap', [
                'slug' => $slug,
                'links' => $this->publishedPages()->map(fn (Page $p) => ['slug' => $p->slug, 'title' => $p->content()['title']]),
            ]);
        }

        $page = Page::where('slug', $slug)->where('is_published', true)->first();
        abort_unless($page, 404);

        return view('site.page', [
            'page' => $page,
            'meta' => $page->content(),
            'blocks' => $page->blocks()->where('is_visible', true)->get(),
            'slug' => $slug,
        ]);
    }

    public function sitemapXml()
    {
        return response()
            ->view('site.sitemap-xml', ['pages' => $this->publishedPages(), 'languages' => Site::languages()])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function publishedPages()
    {
        return Page::where('is_published', true)->orderBy('slug')->get();
    }

    public function contact(Request $request)
    {
        $this->applyLocale($request);
        if ($request->filled('website')) { // armadilha para robôs: finge sucesso
            return back()->with('contact_ok', Site::settings()['msg_contact_ok']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:190',
            'phone' => 'nullable|string|max:40',
            'subject' => 'nullable|string|max:150',
            'other' => 'nullable|string|max:150',
            'message' => 'required|string|max:5000',
        ]);
        Captcha::verify($request);

        $data['subject'] = ($data['other'] ?? null) ?: ($data['subject'] ?? null);
        unset($data['other']);
        $message = ContactMessage::create($data + ['locale' => app()->getLocale()]);

        $this->notify($message);

        return back()->with('contact_ok', Site::settings()['msg_contact_ok']);
    }

    public function subscribe(Request $request)
    {
        $this->applyLocale($request);
        $data = $request->validateWithBag('subscribe', ['email' => 'required|email|max:190']);
        Subscriber::firstOrCreate(['email' => strtolower($data['email'])], ['locale' => app()->getLocale()]);

        return back()->with('subscribe_ok', Site::settings()['msg_subscribe_ok']);
    }

    private function applyLocale(Request $request): void
    {
        $locale = (string) $request->input('locale');
        app()->setLocale(Site::languages()->contains('code', $locale) ? $locale : Site::defaultLocale());
    }

    /** A mensagem já está salva no banco; falha de e-mail não pode derrubar o envio. */
    private function notify(ContactMessage $m): void
    {
        $to = Site::settings()['notify_email'] ?? null;
        if (! $to) {
            return;
        }
        try {
            Mail::raw("Nome: {$m->name}\nE-mail: {$m->email}\nTelefone: {$m->phone}\nAssunto: {$m->subject}\n\n{$m->message}", function ($mail) use ($to, $m) {
                $mail->to($to)->replyTo($m->email)->subject('Contato pelo site: '.($m->subject ?: $m->name));
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
