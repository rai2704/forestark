<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\Subscriber;
use App\Support\Content;
use App\Support\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function index()
    {
        return view('admin.newsletters.index', [
            'newsletters' => Newsletter::latest()->paginate(20),
            'confirmed' => Subscriber::confirmed()->count(),
            'canSend' => Site::canSendMail(),
        ]);
    }

    public function create()
    {
        return view('admin.newsletters.form', ['newsletter' => new Newsletter(['data' => []])]);
    }

    public function store(Request $request)
    {
        $newsletter = Newsletter::create(['data' => $this->payload($request)]);

        return redirect()->route('admin.newsletters.edit', $newsletter)->with('ok', 'Rascunho salvo.');
    }

    public function edit(Newsletter $newsletter)
    {
        abort_if($newsletter->sent_at, 422, 'Esta newsletter já foi enviada.');

        return view('admin.newsletters.form', ['newsletter' => $newsletter]);
    }

    public function update(Request $request, Newsletter $newsletter)
    {
        abort_if($newsletter->sent_at, 422, 'Esta newsletter já foi enviada.');
        $newsletter->update(['data' => $this->payload($request)]);

        return back()->with('ok', 'Rascunho salvo.');
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();

        return back()->with('ok', 'Newsletter excluída.');
    }

    public function send(Newsletter $newsletter)
    {
        if (! Site::canSendMail()) {
            return back()->withErrors(['email' => 'O envio de e-mail (SMTP) ainda não está configurado no servidor.']);
        }
        $default = $newsletter->content(Site::defaultLocale());
        if (trim((string) $default['subject']) === '' || trim(strip_tags((string) $default['body'])) === '') {
            return back()->withErrors(['email' => 'Preencha o assunto e o conteúdo no idioma padrão antes de enviar.']);
        }

        // Marca antes de enviar para que um clique duplo não dispare duas vezes.
        if (! Newsletter::whereKey($newsletter->id)->whereNull('sent_at')->update(['sent_at' => now()])) {
            return back()->withErrors(['email' => 'Esta newsletter já foi enviada.']);
        }

        set_time_limit(0);
        $sent = 0;
        $failed = 0;
        $siteName = Site::settings()['site_name'];

        Subscriber::confirmed()->chunkById(100, function ($subscribers) use ($newsletter, $siteName, &$sent, &$failed) {
            foreach ($subscribers as $s) {
                $locale = $s->locale ?: Site::defaultLocale();
                $content = $newsletter->content($locale);
                try {
                    Mail::to($s->email)->send(new NewsletterMail($content['subject'], $content['body'], route('site.newsletter.cancel', $s->token), $siteName, $locale));
                    $sent++;
                } catch (\Throwable $e) {
                    report($e);
                    $failed++;
                }
            }
        });

        $newsletter->update(['sent_count' => $sent, 'failed_count' => $failed]);

        return back()->with('ok', "Newsletter enviada: {$sent} entregue(s) ao servidor de e-mail, {$failed} com falha.");
    }

    private function payload(Request $request): array
    {
        return Content::fromInput(config('portal.newsletter_fields'), (array) $request->input('data', []));
    }
}
