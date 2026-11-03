<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Entry;
use App\Models\Page;
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

        foreach (config('portal.collections') as $key => $def) {
            $route = $def['detail'] ?? null;
            if ($route && str_starts_with($slug, $route.'/')) {
                return $this->entry($key, $route, substr($slug, strlen($route) + 1), $slug);
            }
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

    private function entry(string $collection, string $route, string $entrySlug, string $slug)
    {
        $entry = Entry::where('collection', $collection)->where('slug', $entrySlug)->published()->first();
        abort_unless($entry?->publicUrl(), 404);

        return view('site.entry', [
            'entry' => $entry,
            'c' => $entry->content(),
            'route' => $route,
            'slug' => $slug,
            'sectionTitle' => Page::where('slug', $route)->first()?->content()['title'] ?? config("portal.collections.$collection.label"),
        ]);
    }

    public function sitemapXml()
    {
        return response()
            ->view('site.sitemap-xml', ['pages' => $this->sitemapItems(), 'languages' => Site::languages()])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /** Páginas publicadas mais as páginas de detalhe que existem. */
    private function sitemapItems()
    {
        $items = $this->publishedPages()->map(fn (Page $p) => (object) ['slug' => $p->slug, 'updated_at' => $p->updated_at]);

        foreach (config('portal.collections') as $key => $def) {
            if ($route = $def['detail'] ?? null) {
                Entry::of($key)->published()->get()->filter->publicUrl()->each(function (Entry $e) use ($items, $route) {
                    $items->push((object) ['slug' => $route.'/'.$e->slug, 'updated_at' => $e->updated_at]);
                });
            }
        }

        return $items;
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
