<?php

namespace Tests\Feature;

use App\Mail\ConfirmSubscriptionMail;
use App\Mail\NewsletterMail;
use App\Models\Block;
use App\Models\ContactMessage;
use App\Models\Entry;
use App\Models\Language;
use App\Models\MenuItem;
use App\Models\Newsletter;
use App\Models\Page;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\Subscriber;
use App\Models\User;
use App\Support\Site;
use App\Support\VideoUrl;
use Database\Seeders\PortalContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Site::flush();
        $this->seed(PortalContentSeeder::class);
        Site::flush();
    }

    private function admin(?array $permissions = null): User
    {
        $role = Role::firstOrCreate(['name' => $permissions === null ? 'Total' : 'Limitado'], ['full_access' => $permissions === null]);

        return User::factory()->create(['role_id' => $role->id, 'is_active' => true, 'permissions' => $permissions ?? []]);
    }

    public function test_pages_render_in_each_language(): void
    {
        $this->get('/')->assertOk()->assertSee('PLANO DE NEGÓCIO')->assertSee('lang="pt"', false);
        $this->get('/en')->assertOk()->assertSee('BUSINESS PLAN')->assertSee('lang="en"', false);
        $this->get('/es')->assertOk()->assertSee('PLAN DE NEGOCIO');
        $this->get('/en/a-empresa')->assertOk()->assertSee('Certifications');
        $this->get('/es/servicos')->assertOk()->assertSee('Descargar PMFS');
        $this->get('/contato')->assertOk()->assertSee('Solicite um retorno de contato');
    }

    public function test_footer_pages_exist_and_only_listed_documents_are_linked(): void
    {
        foreach (['casos-de-sucesso', 'noticias', 'portifolio'] as $slug) {
            $this->get("/$slug")->assertOk();
            $this->get("/en/$slug")->assertOk();
        }
        $this->get('/')->assertDontSee('contrato-001-2022.pdf')->assertSee('Imaflora');

        $block = Block::where('type', 'about_accordion')->first();
        $data = $block->data;
        $data['documents'] = [
            ['label' => ['pt' => 'Contrato'], 'file' => 'uploads/servicos/pmfs.pdf', 'url' => ''],
            ['label' => ['pt' => 'Sem arquivo'], 'file' => '', 'url' => ''],
        ];
        $block->update(['data' => $data]);
        $this->get('/')->assertSee('Contrato')->assertSee('uploads/servicos/pmfs.pdf')->assertDontSee('Sem arquivo');
    }

    public function test_production_redirects_http_to_https(): void
    {
        $this->app['env'] = 'production';
        $this->get('http://forestark.test/contato?a=1')->assertRedirect('https://forestark.test/contato?a=1')->assertStatus(301);
        $this->get('https://forestark.test/contato')->assertOk();
    }

    public function test_language_selector_uses_flags_and_accessibility_tools_are_present(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('flags/br.svg')->assertSee('flags/us.svg')->assertSee('flags/es.svg')
            ->assertSee('data-a11y-font', false)->assertSee('data-a11y-contrast', false)
            ->assertSee('id="conteudo"', false)->assertSee('Ir para o conteúdo principal')
            ->assertSee('vlibras-plugin.js');
        $this->get('/en')->assertSee('Skip to main content')->assertDontSee('vlibras-plugin.js');
        foreach (['br', 'us', 'es'] as $flag) {
            $this->assertFileExists(public_path("flags/$flag.svg"));
        }
    }

    public function test_sitemap_lists_only_published_pages_in_every_language(): void
    {
        Page::where('slug', 'portifolio')->update(['is_published' => false]);

        $this->get('/mapa-do-site')->assertOk()->assertSee('Mapa do site')->assertViewHas('links', fn ($links) => $links->pluck('slug')->contains('a-empresa') && ! $links->pluck('slug')->contains('portifolio'));
        $this->get('/en/mapa-do-site')->assertOk()->assertSee('Site map')->assertSee('The Company');

        $xml = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $xml->assertSee('/en/a-empresa', false)->assertSee('/es/servicos', false)->assertSee('hreflang="en"', false)->assertDontSee('portifolio', false);
        $this->assertNotFalse(simplexml_load_string($xml->getContent()));
    }

    public function test_footer_shows_developer_credit_and_admin_lock(): void
    {
        $this->get('/')->assertSee('Desenvolvido por')->assertSee('GAPI Tecnologia &amp; Sistemas', false)
            ->assertSee('Soluções Inteligentes em Arquitetura Web')->assertSee('fa-lock', false)->assertSee(url('admin'), false);
        $this->get('/en')->assertSee('Developed by');
    }

    public function test_admin_lock_opens_in_new_tab_and_login_page_renders(): void
    {
        $this->get('/')->assertSee('target="_blank" rel="nofollow noopener"', false);
        $this->get('/admin/login')->assertOk()->assertSee('Área Restrita')->assertSee('Esqueceu sua senha?');
        $this->get('/admin/recuperar-senha')->assertOk();
        $this->get('/admin/redefinir-senha/abc?email=a@example.com')->assertOk()->assertSee('a@example.com');
    }

    public function test_password_recovery_does_not_reveal_accounts_and_resets_password(): void
    {
        $user = $this->admin();
        $generic = 'Se esse e-mail estiver cadastrado e ativo, enviamos um link de redefinição para ele.';

        $this->post('/admin/recuperar-senha', ['email' => 'ninguem@example.com'])->assertSessionHas('ok', $generic);
        $this->post('/admin/recuperar-senha', ['email' => $user->email])->assertSessionHas('ok', $generic);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);

        $this->post('/admin/redefinir-senha', ['token' => 'errado', 'email' => $user->email, 'password' => 'senha-nova-123', 'password_confirmation' => 'senha-nova-123'])->assertSessionHasErrors('email');

        $token = Password::createToken($user);
        $this->post('/admin/redefinir-senha', ['token' => $token, 'email' => $user->email, 'password' => 'senha-nova-123', 'password_confirmation' => 'senha-nova-123'])->assertRedirect('/admin/login');
        $this->assertTrue(Hash::check('senha-nova-123', $user->fresh()->password));
    }

    public function test_contact_requires_captcha(): void
    {
        $payload = ['name' => 'Ana', 'email' => 'ana@example.com', 'message' => 'Oi'];

        $this->get('/contato')->assertOk()->assertSee('Quanto é')->assertSee('name="captcha"', false)->assertSee('Informações de Contato');
        $this->post('/contato-enviar', $payload)->assertSessionHasErrors('captcha');
        $this->withSession(['captcha.contact' => ['q' => '3 + 4', 'a' => 7]])->post('/contato-enviar', $payload + ['captcha' => '8'])->assertSessionHasErrors('captcha');
        $this->withSession(['captcha.contact' => ['q' => '3 + 4', 'a' => 7]])->post('/contato-enviar', $payload + ['captcha' => 'x'])->assertSessionHasErrors('captcha');
        $this->assertSame(0, ContactMessage::count());

        $this->withSession(['captcha.contact' => ['q' => '3 + 4', 'a' => 7]])->post('/contato-enviar', $payload + ['captcha' => '7'])->assertSessionHasNoErrors();
        $this->assertSame(1, ContactMessage::count());
        $this->post('/contato-enviar', $payload + ['captcha' => '7'])->assertSessionHasErrors('captcha');
    }

    public function test_contact_uses_turnstile_when_configured(): void
    {
        config(['services.turnstile.site_key' => 'site', 'services.turnstile.secret_key' => 'secret']);
        $payload = ['name' => 'Ana', 'email' => 'ana@example.com', 'message' => 'Oi'];

        $this->get('/contato')->assertSee('cf-turnstile', false)->assertDontSee('name="captcha"', false);
        $this->post('/contato-enviar', $payload)->assertSessionHasErrors('captcha');

        Http::fake(['challenges.cloudflare.com/*' => Http::sequence()->push(['success' => false])->push(['success' => true])]);
        $this->post('/contato-enviar', $payload + ['cf-turnstile-response' => 'ruim'])->assertSessionHasErrors('captcha');

        $this->post('/contato-enviar', $payload + ['cf-turnstile-response' => 'bom'])->assertSessionHasNoErrors();
        $this->assertSame(1, ContactMessage::count());
    }

    public function test_subscription_requires_email_confirmation_when_mail_is_configured(): void
    {
        config(['mail.default' => 'smtp']);
        Mail::fake();

        $response = $this->withSession(['captcha.subscribe' => ['q' => '1 + 1', 'a' => 2]])->from('/en/contato')->post('/inscricao', ['email' => 'Ana@Example.com', 'locale' => 'en', 'captcha' => '2']);
        $response->assertRedirect(url('/en/contato').'#inscricao')->assertSessionHas('subscribe_ok', 'Almost there! We sent you an email to confirm your subscription.');

        $s = Subscriber::first();
        $this->assertSame('ana@example.com', $s->email);
        $this->assertNull($s->confirmed_at);
        Mail::assertSent(ConfirmSubscriptionMail::class, fn ($m) => $m->hasTo('ana@example.com') && str_contains($m->url, $s->token));

        $this->get("/newsletter/confirmar/{$s->token}")->assertOk()->assertSee('Subscription confirmed');
        $this->assertNotNull($s->fresh()->confirmed_at);

        // Reinscrição não reenvia nem revela que o e-mail já está na lista.
        Mail::fake();
        $this->withSession(['captcha.subscribe' => ['q' => '1 + 1', 'a' => 2]])->post('/inscricao', ['email' => 'ana@example.com', 'locale' => 'en', 'captcha' => '2'])->assertSessionHas('subscribe_ok', 'Almost there! We sent you an email to confirm your subscription.');
        Mail::assertNothingSent();
    }

    public function test_subscription_confirms_directly_without_smtp_and_blocks_bots(): void
    {
        $this->withSession(['captcha.subscribe' => ['q' => '1 + 1', 'a' => 2]])->post('/inscricao', ['email' => 'a@example.com', 'locale' => 'pt', 'captcha' => '2'])->assertSessionHas('subscribe_ok', 'Inscrição realizada. Obrigado!');
        $this->assertNotNull(Subscriber::first()->confirmed_at);

        $this->post('/inscricao', ['email' => 'bot@example.com', 'website' => 'x']);
        $this->post('/inscricao', ['email' => 'invalido'])->assertSessionHasErrors('email', null, 'subscribe');
        $this->assertSame(1, Subscriber::count());
    }

    public function test_unsubscribe_needs_post_and_removes_the_address(): void
    {
        $s = Subscriber::create(['email' => 'a@example.com', 'locale' => 'pt', 'confirmed_at' => now()]);

        $this->get("/newsletter/cancelar/{$s->token}")->assertOk()->assertSee('a@example.com');
        $this->assertSame(1, Subscriber::count());
        $this->post("/newsletter/cancelar/{$s->token}")->assertOk()->assertSee('Seu e-mail foi removido');
        $this->assertSame(0, Subscriber::count());
        $this->get('/newsletter/cancelar/token-invalido')->assertNotFound();
    }

    public function test_newsletter_goes_once_to_confirmed_subscribers_in_their_language(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/newsletters', ['data' => ['subject' => ['pt' => 'Novidades', 'en' => 'News'], 'body' => ['pt' => '<p>Olá</p><script>x()</script>', 'en' => '<p>Hello</p>']]]);
        $n = Newsletter::first();
        $this->assertStringNotContainsString('script', $n->data['body']['pt']);

        $this->post("/admin/newsletters/{$n->id}/enviar")->assertSessionHasErrors('email');
        $this->assertNull($n->fresh()->sent_at);

        config(['mail.default' => 'smtp']);
        Mail::fake();
        Subscriber::create(['email' => 'pt@example.com', 'locale' => 'pt', 'confirmed_at' => now()]);
        $en = Subscriber::create(['email' => 'en@example.com', 'locale' => 'en', 'confirmed_at' => now()]);
        Subscriber::create(['email' => 'pendente@example.com', 'locale' => 'pt']);

        $this->post("/admin/newsletters/{$n->id}/enviar")->assertSessionHas('ok');
        Mail::assertSentCount(2);
        Mail::assertSent(NewsletterMail::class, fn ($m) => $m->hasTo('en@example.com') && $m->title === 'News' && str_contains($m->unsubscribeUrl, $en->token));
        Mail::assertNotSent(NewsletterMail::class, fn ($m) => $m->hasTo('pendente@example.com'));
        $this->assertSame(2, $n->fresh()->sent_count);

        $this->post("/admin/newsletters/{$n->id}/enviar")->assertSessionHasErrors('email');
        Mail::assertSentCount(2);
    }

    public function test_subscription_requires_captcha_independent_from_contact(): void
    {
        $this->get('/contato')->assertOk()->assertSee('s-captcha', false)->assertSee('c-captcha', false);
        $this->assertNotNull(session('captcha.subscribe'));
        $this->assertNotNull(session('captcha.contact'));
        $question = session('captcha.subscribe')['q'];
        $this->get('/')->assertSee($question);

        $this->from('/contato')->post('/inscricao', ['email' => 'a@example.com'])->assertRedirect(url('/contato').'#inscricao')->assertSessionHasErrors('captcha', null, 'subscribe');
        $errors = ['subscribe' => ['messages' => ['captcha' => ['Erro do rodape']], 'format' => ':message']];
        $page = $this->withSession(['errors' => $errors, '_old_input' => ['email' => 'a@example.com']])->get('/contato');
        $page->assertSee('Erro do rodape');
        $this->assertSame(1, substr_count($page->getContent(), 'value="a@example.com"'), 'o e-mail volta só no rodapé');
        $this->withSession(['captcha.subscribe' => ['q' => '1 + 1', 'a' => 2]])->post('/inscricao', ['email' => 'a@example.com', 'captcha' => '3'])->assertSessionHasErrors('captcha', null, 'subscribe');
        $this->assertSame(0, Subscriber::count());

        // Responder um formulário não consome a pergunta do outro
        $this->withSession(['captcha.subscribe' => ['q' => '1 + 1', 'a' => 2], 'captcha.contact' => ['q' => '3 + 4', 'a' => 7]])
            ->post('/inscricao', ['email' => 'a@example.com', 'captcha' => '2'])->assertSessionHasNoErrors();
        $this->assertSame(1, Subscriber::count());
        $this->assertSame(7, session('captcha.contact')['a']);
        $this->assertNull(session('captcha.subscribe'));
    }

    public function test_subscription_uses_turnstile_when_configured(): void
    {
        config(['services.turnstile.site_key' => 'site', 'services.turnstile.secret_key' => 'secret']);

        $response = $this->get('/');
        $response->assertSee('cf-turnstile', false)->assertDontSee('s-captcha', false);
        $this->assertSame(1, substr_count($response->getContent(), 'turnstile/v0/api.js'));
        $contact = $this->get('/contato');
        $this->assertSame(1, substr_count($contact->getContent(), 'turnstile/v0/api.js'));
        $this->assertSame(2, substr_count($contact->getContent(), 'class="cf-turnstile'));

        $this->post('/inscricao', ['email' => 'a@example.com'])->assertSessionHasErrors('captcha', null, 'subscribe');
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);
        $this->post('/inscricao', ['email' => 'a@example.com', 'cf-turnstile-response' => 'bom'])->assertSessionHasNoErrors();
        $this->assertSame(1, Subscriber::count());
    }

    public function test_footer_bar_is_fixed(): void
    {
        $this->get('/')->assertSee('data-footer-bar class="fixed inset-x-0 bottom-0', false);
    }

    public function test_new_language_requires_valid_flag(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/languages', ['code' => 'fr', 'name' => 'Français', 'flag' => '../x'])->assertSessionHasErrors('flag');
        $this->post('/admin/languages', ['code' => 'fr', 'name' => 'Français', 'flag' => 'fr'])->assertSessionHasNoErrors();
        $this->assertSame('fr', Language::where('code', 'fr')->first()->flag);
    }

    public function test_missing_translation_falls_back_to_default_language(): void
    {
        Language::create(['code' => 'fr', 'name' => 'Français', 'position' => 9]);
        Site::flush();
        $this->get('/fr')->assertOk()->assertSee('PLANO DE NEGÓCIO')->assertSee('lang="fr"', false);
    }

    public function test_unknown_page_and_inactive_language_return_translated_404(): void
    {
        $this->get('/en/nope')->assertNotFound()->assertSee('Nothing was found');
        Language::where('code', 'es')->update(['is_active' => false]);
        Site::flush();
        $this->get('/es/servicos')->assertNotFound();
    }

    public function test_contact_form_stores_message_and_blocks_bots(): void
    {
        $this->withSession(['captcha.contact' => ['q' => '3 + 4', 'a' => 7]])->post('/contato-enviar', ['name' => 'Ana', 'email' => 'ana@example.com', 'message' => 'Oi', 'subject' => 'Vendas', 'locale' => 'en', 'captcha' => '7'])->assertSessionHas('contact_ok', 'Message sent. We will contact you soon.');
        $this->assertSame('en', ContactMessage::first()->locale);

        $this->post('/contato-enviar', ['name' => 'Bot', 'email' => 'b@example.com', 'message' => 'spam', 'website' => 'x']);
        $this->assertSame(1, ContactMessage::count());
    }

    public function test_each_action_requires_its_own_permission(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $user = $this->admin(['menus' => ['acessar' => true]]);
        $this->actingAs($user);
        $item = MenuItem::first();

        $this->get('/admin/menus')->assertOk()->assertDontSee(route('admin.menus.create', ['location' => 'header']), false);
        $this->get('/admin/pages')->assertForbidden();
        $this->get('/admin/menus/create')->assertForbidden();
        $this->get("/admin/menus/{$item->id}/edit")->assertForbidden();
        $this->delete("/admin/menus/{$item->id}")->assertForbidden();
        $this->assertNotNull($item->fresh());

        $user->update(['permissions' => ['menus' => ['acessar' => true, 'excluir' => true]]]);
        $this->delete("/admin/menus/{$item->id}")->assertRedirect();
        $this->assertNull($item->fresh());
    }

    public function test_user_form_saves_permission_grid(): void
    {
        $this->actingAs($this->admin());
        $role = Role::where('name', 'Usuário')->first();

        $this->post('/admin/users', ['name' => 'Ana', 'email' => 'ana@example.com', 'role_id' => $role->id, 'is_active' => 1, 'password' => 'senha-longa-123', 'password_confirmation' => 'senha-longa-123',
            'perm' => ['pages' => ['acessar' => 1, 'editar' => 1], 'inexistente' => ['acessar' => 1]]])->assertRedirect('/admin/users');

        $ana = User::where('email', 'ana@example.com')->first();
        $this->assertEquals(['pages' => ['acessar' => true, 'editar' => true]], $ana->permissions);
        $this->assertTrue($ana->canAccess('pages', 'editar'));
        $this->assertFalse($ana->canAccess('pages', 'excluir'));

        $this->get('/admin/roles')->assertOk()->assertSee('Matriz de permissões')->assertSee('Ana');
    }

    public function test_limited_user_cannot_escalate_privileges(): void
    {
        $limited = $this->admin(['users' => ['acessar' => true, 'incluir' => true, 'editar' => true, 'excluir' => true]]);
        $full = $this->admin();
        $fullRole = $full->role;
        $this->actingAs($limited);

        $this->post('/admin/users', ['name' => 'X', 'email' => 'x@example.com', 'role_id' => $fullRole->id, 'password' => 'senha-longa-123', 'password_confirmation' => 'senha-longa-123'])->assertForbidden();
        $this->put("/admin/users/{$limited->id}", ['name' => 'L', 'email' => $limited->email, 'role_id' => $limited->role_id, 'perm' => ['settings' => ['acessar' => 1]]])->assertForbidden();
        $this->get("/admin/users/{$full->id}/edit")->assertForbidden();
        $this->delete("/admin/users/{$full->id}")->assertForbidden();
        $this->assertFalse($limited->fresh()->canAccess('settings'));
    }

    public function test_block_items_have_listing_and_their_own_edit_screen(): void
    {
        $this->actingAs($this->admin());
        $about = Block::where('type', 'about_accordion')->first();
        $url = "/admin/blocks/{$about->id}/itens/documents";

        $this->post($url, ['data' => ['label' => ['pt' => 'Contrato'], 'url' => 'https://example.com/a.pdf']])->assertRedirect("/admin/blocks/{$about->id}/edit");
        $this->post($url, ['data' => ['label' => ['pt' => 'Laudo'], 'url' => 'https://example.com/b.pdf']]);
        $this->get("/admin/blocks/{$about->id}/edit")->assertOk()->assertSee('Contrato')->assertSee("$url/0/edit", false);
        $this->get("$url/0/edit")->assertOk()->assertSee('name="data[label][en]"', false);
        $this->get("$url/99/edit")->assertNotFound();
        $this->get("/admin/blocks/{$about->id}/itens/inexistente/novo")->assertNotFound();

        $this->put("$url/1", ['data' => ['label' => ['pt' => 'Editado'], 'url' => 'https://example.com/b.pdf']]);
        $this->post("$url/1/move", ['direction' => 'up']);
        $this->assertSame('Editado', $about->fresh()->data['documents'][0]['label']['pt']);
        $this->delete("$url/0");
        $this->assertCount(1, $about->fresh()->data['documents']);

        $this->put("/admin/blocks/{$about->id}", ['data' => ['title' => ['pt' => 'Plano']]]);
        $this->assertSame('Plano', $about->fresh()->data['title']['pt']);
        $this->assertCount(1, $about->fresh()->data['documents']);
        $this->assertSame('plan', $about->fresh()->source());
    }

    public function test_existing_content_moved_to_modules_without_loss(): void
    {
        foreach (['gallery' => 0, 'videos' => 0, 'team' => 0, 'banners' => 3, 'testimonials' => 2, 'plan' => 4, 'faq' => 5, 'news' => 5, 'partners' => 5, 'success_cases' => 3, 'portfolio' => 4, 'values' => 3, 'certifications' => 11, 'services' => 4, 'subjects' => 4] as $collection => $count) {
            $this->assertSame($count, Entry::where('collection', $collection)->count(), $collection);
        }
        $this->get('/')->assertSee('Forest Ark Investiments')->assertSee('COMERCIALIZAÇÃO DA MADEIRA')->assertSee('Um Tesouro da Amazônia')->assertSee('Lemafon');
        $this->get('/en/a-empresa')->assertSee('Principle:')->assertSee('Certifications');
        $this->get('/servicos')->assertSee('Baixar PMFS');
        $this->get('/contato')->assertSee('Vendas');
        $this->get('/sobre-nos')->assertOk()->assertSee('Contrato de Concessão Florestal');
        $this->get('/en/sobre-nos')->assertSee('About Us');
    }

    public function test_every_module_has_full_crud_with_its_own_permission(): void
    {
        $user = $this->admin();
        $this->actingAs($user);

        foreach (array_keys(config('portal.collections')) as $collection) {
            $base = "/admin/conteudo/$collection";
            $this->get($base)->assertOk();
            $this->get("$base/novo")->assertOk();
            $first = config("portal.collections.$collection.fields");
            $name = collect($first)->firstWhere('tr', true)['name'];
            $video = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
            $photoAlbum = config("portal.collections.$collection.photos") ?? false;
            $this->post($base, ['is_published' => 1, 'data' => [$name => ['pt' => 'Registro E2E'], 'video' => $video]])->assertRedirect();
            $entry = Entry::where('collection', $collection)->latest('id')->first();
            $this->assertStringContainsString('Registro E2E', $entry->data[$name]['pt']);

            $this->get("$base/{$entry->id}/edit")->assertOk();
            $this->put("$base/{$entry->id}", ['data' => [$name => ['pt' => 'Alterado'], 'video' => $video]])->assertRedirect($base);
            $this->assertFalse($entry->fresh()->is_published);
            $this->post("$base/{$entry->id}/toggle");
            $this->assertTrue($entry->fresh()->is_published);
            $this->delete("$base/{$entry->id}");
            $this->assertNull($entry->fresh());
        }

        $this->get('/admin/conteudo/news')->assertSee('Árvores Imponentes')->assertSee('Vida Selvagem');
        $this->get('/admin/conteudo/inexistente')->assertNotFound();
        $news = Entry::where('collection', 'news')->first();
        $this->get("/admin/conteudo/banners/{$news->id}/edit")->assertNotFound();

        $limited = $this->admin(['news' => ['acessar' => true]]);
        $this->actingAs($limited);
        $this->get('/admin/conteudo/news')->assertOk()->assertDontSee('/admin/conteudo/news/novo', false);
        $this->get('/admin/conteudo/news/novo')->assertForbidden();
        $this->get('/admin/conteudo/banners')->assertForbidden();
        $this->delete("/admin/conteudo/news/{$news->id}")->assertForbidden();
    }

    public function test_entry_detail_page_exists_only_with_full_content_and_when_published(): void
    {
        $entry = Entry::where('collection', 'news')->first();
        $this->get("/noticias/{$entry->slug}")->assertNotFound();

        $data = $entry->data;
        $data['body'] = ['pt' => '<p>Texto completo</p>', 'en' => '<p>Full text</p>'];
        $entry->update(['data' => $data]);

        $this->get("/noticias/{$entry->slug}")->assertOk()->assertSee('Texto completo');
        $this->get("/en/noticias/{$entry->slug}")->assertOk()->assertSee('Full text');
        $this->get('/noticias')->assertSee("/noticias/{$entry->slug}", false);
        $this->get('/sitemap.xml')->assertSee("/noticias/{$entry->slug}", false);

        $entry->update(['is_published' => false]);
        $this->get("/noticias/{$entry->slug}")->assertNotFound();
        $this->get('/noticias')->assertDontSee("/noticias/{$entry->slug}", false);
    }

    public function test_block_source_can_be_switched(): void
    {
        $this->actingAs($this->admin());
        $block = Block::where('type', 'cards')->whereHas('page', fn ($q) => $q->where('slug', 'noticias'))->first();

        $this->put("/admin/blocks/{$block->id}", ['source' => 'portfolio'])->assertSessionHasNoErrors();
        $this->assertSame('portfolio', $block->fresh()->source());
        $this->assertSame('Últimas notícias e atualizações', $block->fresh()->data['title']['pt']);
        $this->put("/admin/blocks/{$block->id}", ['source' => 'banners'])->assertSessionHasErrors('source');
    }

    public function test_every_image_field_informs_its_ideal_size(): void
    {
        $walk = function (array $fields) use (&$walk) {
            foreach ($fields as $f) {
                if ($f['type'] === 'image') {
                    $this->assertCount(2, $f['size'] ?? [], "Campo {$f['name']} sem tamanho ideal");
                }
                if ($f['type'] === 'repeater') {
                    $walk($f['fields']);
                }
            }
        };
        foreach (config('portal.blocks') as $block) {
            $walk($block['fields']);
        }
        foreach (config('portal.collections') as $collection) {
            $walk($collection['fields']);
        }
        foreach (config('portal.settings_groups') as $fields) {
            $walk($fields);
        }

        $this->actingAs($this->admin());
        $banner = Entry::where('collection', 'banners')->first();
        $this->get("/admin/conteudo/banners/{$banner->id}/edit")->assertSee('Tamanho ideal: 1920 × 1000 px')->assertSee('Imagem atual: 1000 × 500 px')->assertSee('menor que o ideal');
        $this->get('/admin/configuracoes/identidade')->assertSee('Tamanho ideal: 200 × 103 px');
    }

    public function test_languages_are_listed_and_edited_on_their_own_screen(): void
    {
        $this->actingAs($this->admin());
        $en = Language::where('code', 'en')->first();

        $this->get('/admin/languages')->assertOk()->assertSee("/admin/languages/{$en->id}/edit", false)->assertDontSee('name="name"', false)->assertDontSee('name="flag"', false);
        $this->get("/admin/languages/{$en->id}/edit")->assertOk()->assertSee('name="flag"', false);
    }

    public function test_video_accepts_only_youtube_or_vimeo_links(): void
    {
        foreach ([
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            'https://youtu.be/dQw4w9WgXcQ' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            'https://www.youtube.com/shorts/dQw4w9WgXcQ' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            'https://vimeo.com/123456789' => 'https://player.vimeo.com/video/123456789',
            'https://vimeo.com/123456789/abcdef1234' => 'https://player.vimeo.com/video/123456789?h=abcdef1234',
            'https://player.vimeo.com/video/123456789' => 'https://player.vimeo.com/video/123456789',
        ] as $url => $embed) {
            $this->assertSame($embed, VideoUrl::embed($url), $url);
        }
        foreach (['https://example.com/video.mp4', 'javascript:alert(1)', 'https://youtube.com.evil.com/watch?v=dQw4w9WgXcQ', 'https://vimeo.com/abc', '/uploads/video.mp4', ''] as $bad) {
            $this->assertNull(VideoUrl::embed($bad), $bad);
        }

        $this->actingAs($this->admin());
        $this->post('/admin/conteudo/videos', ['is_published' => 1, 'data' => ['title' => ['pt' => 'X'], 'video' => 'https://example.com/a.mp4']])->assertSessionHasErrors('video');
        $this->post('/admin/conteudo/videos', ['is_published' => 1, 'data' => ['title' => ['pt' => 'X']]])->assertSessionHasErrors('video');
        $this->assertSame(0, Entry::where('collection', 'videos')->count());
    }

    public function test_sections_stay_hidden_until_they_have_published_content(): void
    {
        $this->get('/')->assertDontSee('Galeria de Fotos')->assertDontSee('>Equipe<', false);
        $this->get('/mapa-do-site')->assertDontSee('Galeria de Fotos');
        $this->get('/sitemap.xml')->assertDontSee('/equipe', false);
        $this->get('/equipe')->assertOk()->assertSee('Em breve.');

        $person = Entry::create(['collection' => 'team', 'position' => 0, 'is_published' => true, 'data' => ['title' => ['pt' => 'Ana Lima', 'en' => 'Ana Lima'], 'role' => ['pt' => 'Diretora', 'en' => 'Director'], 'text' => [], 'image' => '', 'email' => 'ana@example.com']]);
        $this->get('/')->assertSee('/equipe', false);
        $this->get('/equipe')->assertSee('Ana Lima')->assertSee('Diretora');
        $this->get('/en/equipe')->assertSee('Director');
        $this->get('/sitemap.xml')->assertSee('/equipe', false);

        $person->update(['is_published' => false]);
        $this->get('/equipe')->assertDontSee('Ana Lima');
        $this->get('/mapa-do-site')->assertDontSee('>Equipe<', false);

        $video = Entry::create(['collection' => 'videos', 'position' => 0, 'is_published' => true, 'data' => ['title' => ['pt' => 'Filme'], 'text' => [], 'video' => 'https://youtu.be/dQw4w9WgXcQ']]);
        $this->get('/videos')->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
        $video->update(['is_published' => false]);
        $this->get('/videos')->assertDontSee('youtube-nocookie.com', false);
    }

    public function test_album_photos_can_be_managed_and_hidden(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/conteudo/gallery', ['is_published' => 1, 'data' => ['title' => ['pt' => 'Campo 2026']]])->assertRedirect();
        $album = Entry::where('collection', 'gallery')->first();
        $base = "/admin/conteudo/gallery/{$album->id}";
        $this->assertSame('campo-2026', $album->slug);
        $this->get('/galeria/campo-2026')->assertNotFound();
        $this->get("$base/edit")->assertSee('fotos publicadas')->assertDontSee('Conteúdo completo');

        $evil = UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;');
        $this->post("$base/fotos", ['photos' => [UploadedFile::fake()->image('ok.jpg', 800, 600), $evil]])->assertSessionHasErrors('photos');
        $this->assertSame([], $album->fresh()->data['photos'] ?? []);

        $this->post("$base/fotos", ['photos' => [UploadedFile::fake()->image('a.jpg', 800, 600), UploadedFile::fake()->image('b.jpg', 800, 600)]])->assertSessionHasNoErrors();
        $album->refresh();
        $paths = array_column($album->data['photos'], 'image');
        $this->assertCount(2, $paths);

        $this->get('/galeria/campo-2026')->assertOk()->assertSee($paths[0], false)->assertSee($paths[1], false)->assertSee('data-lightbox', false);
        $this->get('/galeria')->assertOk()->assertSee('/galeria/campo-2026', false);

        $this->post("$base/fotos/0/toggle");
        $this->get('/galeria/campo-2026')->assertDontSee($paths[0], false)->assertSee($paths[1], false);
        $this->post("$base/fotos/1/move", ['direction' => 'up']);
        $this->assertSame($paths[1], $album->fresh()->data['photos'][0]['image']);

        $this->put($base, ['is_published' => 1, 'data' => ['title' => ['pt' => 'Campo 2026']]]);
        $this->assertCount(2, $album->fresh()->data['photos'], 'salvar o álbum mantém as fotos');

        $this->post("$base/toggle");
        $this->get('/galeria/campo-2026')->assertNotFound();
        $this->post("$base/toggle");

        $this->delete("$base/fotos/1");
        $this->delete("$base/fotos/0");
        $this->assertSame([], $album->fresh()->data['photos']);
        $this->get('/galeria/campo-2026')->assertNotFound();
        foreach ($paths as $path) {
            @unlink(public_path($path));
        }
    }

    public function test_block_item_can_be_hidden_from_site(): void
    {
        $this->actingAs($this->admin());
        $about = Block::where('type', 'about_accordion')->first();
        $url = "/admin/blocks/{$about->id}/itens/documents";
        $this->post($url, ['data' => ['label' => ['pt' => 'Laudo Tecnico'], 'url' => 'https://example.com/a.pdf']]);
        $this->get('/')->assertSee('Laudo Tecnico');
        $this->put("$url/0", ['data' => ['label' => ['pt' => 'Laudo Tecnico'], 'url' => 'https://example.com/a.pdf', 'hidden' => 1]]);
        $this->get('/')->assertDontSee('Laudo Tecnico');
        $this->get("/admin/blocks/{$about->id}/edit")->assertSee('Oculto');
    }

    public function test_images_are_limited_to_2_mb_and_documents_to_5_mb(): void
    {
        $this->actingAs($this->admin());
        $big = fn (string $name = 'grande.jpg') => UploadedFile::fake()->image($name, 800, 600)->size(2049);
        $ok = fn (string $name = 'ok.jpg') => UploadedFile::fake()->image($name, 800, 600)->size(2048);

        $banner = Entry::where('collection', 'banners')->first();
        $before = $banner->data['image'];
        $this->put("/admin/conteudo/banners/{$banner->id}", ['data' => ['image' => $before], 'uploads' => ['image' => $big()]])->assertSessionHasErrors('image');
        $this->assertSame($before, $banner->fresh()->data['image']);

        $this->post('/admin/conteudo/gallery', ['is_published' => 1, 'data' => ['title' => ['pt' => 'Limite']]]);
        $album = Entry::where('collection', 'gallery')->first();
        $this->post("/admin/conteudo/gallery/{$album->id}/fotos", ['photos' => [$ok('a.jpg'), $big('b.jpg')]])->assertSessionHasErrors('photos');
        $this->assertSame([], $album->fresh()->data['photos'] ?? [], 'nenhuma foto do envio é gravada se uma passar do limite');

        $this->put('/admin/configuracoes/identidade', ['data' => ['site_name' => 'Forest Ark Investiments'], 'uploads' => ['logo' => $big()]])->assertSessionHasErrors('logo');

        $this->put("/admin/conteudo/banners/{$banner->id}", ['is_published' => 1, 'data' => ['image' => $before], 'uploads' => ['image' => $ok()]])->assertSessionHasNoErrors();
        $saved = $banner->fresh()->data['image'];
        $this->assertNotSame($before, $saved);
        @unlink(public_path($saved));

        $pdf = UploadedFile::fake()->createWithContent('manual.pdf', "%PDF-1.4\n".str_repeat('0', 3 * 1024 * 1024));
        $service = Entry::where('collection', 'services')->first();
        $this->put("/admin/conteudo/services/{$service->id}", ['is_published' => 1, 'data' => ['file' => ''], 'uploads' => ['file' => $pdf]])->assertSessionHasNoErrors();
        $file = $service->fresh()->data['file'];
        $this->assertStringEndsWith('.pdf', $file);
        @unlink(public_path($file));

        $tooBig = UploadedFile::fake()->createWithContent('grande.pdf', '%PDF-1.4
'.str_repeat('0', 5 * 1024 * 1024 + 1));
        $this->put("/admin/conteudo/services/{$service->id}", ['is_published' => 1, 'data' => ['file' => $file], 'uploads' => ['file' => $tooBig]])->assertSessionHasErrors('file');

        $this->get("/admin/conteudo/banners/{$banner->id}/edit")->assertSee('2 MB');
    }

    public function test_settings_group_keeps_other_groups(): void
    {
        $this->actingAs($this->admin());
        $this->get('/admin/configuracoes')->assertOk()->assertSee('/admin/configuracoes/contato', false);
        $this->get('/admin/configuracoes/inexistente')->assertNotFound();
        $this->put('/admin/configuracoes/contato', ['data' => ['email' => 'novo@example.com']])->assertRedirect('/admin/configuracoes');

        $data = SiteSetting::current()->data;
        $this->assertSame('novo@example.com', $data['email']);
        $this->assertSame('Forest Ark Investiments', $data['site_name']);
    }

    public function test_every_admin_screen_renders(): void
    {
        $this->actingAs($this->admin());
        foreach (['/admin', '/admin/pages', '/admin/pages/create', '/admin/menus', '/admin/menus/create', '/admin/languages', '/admin/configuracoes', '/admin/messages', '/admin/inscritos', '/admin/newsletters', '/admin/newsletters/create', '/admin/users', '/admin/users/create', '/admin/roles', '/admin/languages/create', '/admin/perfil'] as $url) {
            $this->get($url)->assertOk();
        }
        foreach (Block::all() as $block) {
            $this->get("/admin/blocks/{$block->id}/edit")->assertOk();
        }
    }

    public function test_block_save_sanitizes_html_and_rejects_php_upload(): void
    {
        $this->actingAs($this->admin());
        $block = Block::where('type', 'rich_text')->first() ?? Block::create(['page_id' => Page::first()->id, 'type' => 'rich_text', 'data' => [], 'position' => 99]);

        $this->put("/admin/blocks/{$block->id}", ['is_visible' => 1, 'data' => ['title' => ['pt' => 'T', 'en' => 'E'], 'body' => ['pt' => '<p>ok</p><script>alert(1)</script>']]])->assertSessionHasNoErrors();
        $block->refresh();
        $this->assertStringNotContainsString('<script', $block->data['body']['pt']);
        $this->assertSame('E', $block->data['title']['en']);

        $banner = Entry::where('collection', 'banners')->first();
        $evil = UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;');
        $this->put("/admin/conteudo/banners/{$banner->id}", ['data' => ['image' => ''], 'uploads' => ['image' => $evil]])->assertSessionHasErrors('image');
        $this->assertNotSame('', $banner->fresh()->data['image']);
    }

    public function test_last_full_access_user_cannot_be_removed(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $this->put("/admin/users/{$admin->id}", ['name' => 'A', 'email' => $admin->email, 'role_id' => Role::where('name', 'Usuário')->first()->id, 'is_active' => 1])->assertStatus(422);
        $this->assertTrue($admin->fresh()->role->full_access);
    }
}
