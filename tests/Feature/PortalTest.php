<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\ContactMessage;
use App\Models\Language;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use App\Support\Site;
use Database\Seeders\PortalContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    private function admin(array $roleAttrs = ['full_access' => true]): User
    {
        $role = Role::create($roleAttrs + ['name' => 'R'.uniqid(), 'permissions' => []]);

        return User::factory()->create(['role_id' => $role->id, 'is_active' => true]);
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
        $this->post('/contato-enviar', ['name' => 'Ana', 'email' => 'ana@example.com', 'message' => 'Oi', 'subject' => 'Vendas', 'locale' => 'en'])->assertSessionHas('contact_ok', 'Message sent. We will contact you soon.');
        $this->assertSame('en', ContactMessage::first()->locale);

        $this->post('/contato-enviar', ['name' => 'Bot', 'email' => 'b@example.com', 'message' => 'spam', 'website' => 'x']);
        $this->assertSame(1, ContactMessage::count());
    }

    public function test_admin_requires_login_and_permission(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $limited = $this->admin(['full_access' => false, 'permissions' => ['menus']]);
        $this->actingAs($limited)->get('/admin/menus')->assertOk();
        $this->actingAs($limited)->get('/admin/pages')->assertForbidden();
    }

    public function test_every_admin_screen_renders(): void
    {
        $this->actingAs($this->admin());
        foreach (['/admin', '/admin/pages', '/admin/pages/create', '/admin/menus', '/admin/menus/create', '/admin/languages', '/admin/configuracoes', '/admin/messages', '/admin/inscritos', '/admin/users', '/admin/users/create', '/admin/roles/create', '/admin/perfil'] as $url) {
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

        $hero = Block::where('type', 'hero_slider')->first();
        $evil = UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;');
        $this->put("/admin/blocks/{$hero->id}", ['data' => ['items' => [['image' => '']]], 'uploads' => ['items' => [['image' => $evil]]]])->assertSessionHasErrors('image');
        $this->assertCount(3, $hero->fresh()->data['items']);
    }

    public function test_last_full_access_user_cannot_be_removed(): void
    {
        $admin = $this->admin();
        $other = $this->admin(['full_access' => false]);
        $this->actingAs($other);
        $other->role->update(['permissions' => ['users']]);
        $this->delete("/admin/users/{$admin->id}")->assertStatus(422);
    }
}
