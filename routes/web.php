<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\AdminAccess;
use Illuminate\Support\Facades\Route;

$can = fn (string $module, string $action = 'acessar') => AdminAccess::class.":$module,$action";

// Listagem, inclusão, edição e exclusão, cada uma com a sua permissão.
$crud = function (string $name, string $controller, string $module) use ($can) {
    Route::resource($name, $controller)->only('index')->middleware($can($module, 'acessar'));
    Route::resource($name, $controller)->only(['create', 'store'])->middleware($can($module, 'incluir'));
    Route::resource($name, $controller)->only(['edit', 'update'])->middleware($can($module, 'editar'));
    Route::resource($name, $controller)->only('destroy')->middleware($can($module, 'excluir'));
};

Route::prefix('admin')->name('admin.')->group(function () use ($can, $crud) {
    Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [Admin\AuthController::class, 'login'])->middleware('throttle:20,1');
    Route::get('recuperar-senha', [Admin\PasswordResetController::class, 'request'])->name('password.request');
    Route::post('recuperar-senha', [Admin\PasswordResetController::class, 'email'])->middleware('throttle:5,1');
    Route::get('redefinir-senha/{token}', [Admin\PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('redefinir-senha', [Admin\PasswordResetController::class, 'update'])->middleware('throttle:5,1')->name('password.update');

    Route::middleware(['auth', AdminAccess::class])->group(function () use ($can, $crud) {
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', Admin\DashboardController::class)->name('home');
        Route::get('perfil', [Admin\AuthController::class, 'profile'])->name('profile');
        Route::put('perfil', [Admin\AuthController::class, 'updateProfile']);

        $crud('pages', Admin\PageController::class, 'pages');
        Route::get('pages/{page}/blocos', [Admin\PageController::class, 'blocks'])->name('pages.blocks')->middleware($can('pages'));
        Route::get('atalho/{slug}', [Admin\PageController::class, 'shortcut'])->whereIn('slug', ['sobre-nos', 'a-empresa', 'contato'])->name('shortcut')->middleware($can('pages'));

        Route::post('pages/{page}/blocks', [Admin\BlockController::class, 'store'])->name('blocks.store')->middleware($can('pages', 'incluir'));
        Route::post('blocks/{block}/duplicate', [Admin\BlockController::class, 'duplicate'])->name('blocks.duplicate')->middleware($can('pages', 'incluir'));
        Route::delete('blocks/{block}', [Admin\BlockController::class, 'destroy'])->name('blocks.destroy')->middleware($can('pages', 'excluir'));
        Route::middleware($can('pages', 'editar'))->group(function () {
            Route::get('blocks/{block}/edit', [Admin\BlockController::class, 'edit'])->name('blocks.edit');
            Route::put('blocks/{block}', [Admin\BlockController::class, 'update'])->name('blocks.update');
            Route::post('blocks/{block}/move', [Admin\BlockController::class, 'move'])->name('blocks.move');
            Route::post('blocks/{block}/toggle', [Admin\BlockController::class, 'toggle'])->name('blocks.toggle');
            Route::get('blocks/{block}/itens/{repeater}/{index}/edit', [Admin\BlockItemController::class, 'edit'])->whereNumber('index')->name('blocks.items.edit');
            Route::put('blocks/{block}/itens/{repeater}/{index}', [Admin\BlockItemController::class, 'update'])->whereNumber('index')->name('blocks.items.update');
            Route::post('blocks/{block}/itens/{repeater}/{index}/move', [Admin\BlockItemController::class, 'move'])->whereNumber('index')->name('blocks.items.move');
        });
        Route::get('blocks/{block}/itens/{repeater}/novo', [Admin\BlockItemController::class, 'create'])->name('blocks.items.create')->middleware($can('pages', 'incluir'));
        Route::post('blocks/{block}/itens/{repeater}', [Admin\BlockItemController::class, 'store'])->name('blocks.items.store')->middleware($can('pages', 'incluir'));
        Route::delete('blocks/{block}/itens/{repeater}/{index}', [Admin\BlockItemController::class, 'destroy'])->whereNumber('index')->name('blocks.items.destroy')->middleware($can('pages', 'excluir'));

        // Módulos de conteúdo: a permissão é a do próprio módulo.
        Route::prefix('conteudo/{collection}')->whereIn('collection', array_keys(config('portal.collections')))->name('entries.')->group(function () use ($can) {
            Route::get('/', [Admin\EntryController::class, 'index'])->name('index')->middleware($can('collection'));
            Route::get('novo', [Admin\EntryController::class, 'create'])->name('create')->middleware($can('collection', 'incluir'));
            Route::post('/', [Admin\EntryController::class, 'store'])->name('store')->middleware($can('collection', 'incluir'));
            Route::get('{entry}/edit', [Admin\EntryController::class, 'edit'])->name('edit')->middleware($can('collection', 'editar'));
            Route::put('{entry}', [Admin\EntryController::class, 'update'])->name('update')->middleware($can('collection', 'editar'));
            Route::post('{entry}/toggle', [Admin\EntryController::class, 'toggle'])->name('toggle')->middleware($can('collection', 'editar'));
            Route::post('{entry}/move', [Admin\EntryController::class, 'move'])->name('move')->middleware($can('collection', 'editar'));
            Route::delete('{entry}', [Admin\EntryController::class, 'destroy'])->name('destroy')->middleware($can('collection', 'excluir'));
        });

        $crud('menus', Admin\MenuController::class, 'menus');
        Route::post('menus/{menu}/move', [Admin\MenuController::class, 'move'])->name('menus.move')->middleware($can('menus', 'editar'));

        $crud('languages', Admin\LanguageController::class, 'languages');

        Route::get('configuracoes', [Admin\SettingController::class, 'index'])->name('settings')->middleware($can('settings'));
        Route::get('configuracoes/{group}', [Admin\SettingController::class, 'edit'])->name('settings.edit')->middleware($can('settings', 'editar'));
        Route::put('configuracoes/{group}', [Admin\SettingController::class, 'update'])->name('settings.update')->middleware($can('settings', 'editar'));

        Route::get('messages', [Admin\MessageController::class, 'index'])->name('messages.index')->middleware($can('messages'));
        Route::get('messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show')->middleware($can('messages'));
        Route::delete('messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy')->middleware($can('messages', 'excluir'));

        Route::get('inscritos', [Admin\MessageController::class, 'subscribers'])->name('subscribers')->middleware($can('subscribers'));
        Route::delete('inscritos/{subscriber}', [Admin\MessageController::class, 'destroySubscriber'])->name('subscribers.destroy')->middleware($can('subscribers', 'excluir'));

        $crud('newsletters', Admin\NewsletterController::class, 'newsletters');
        Route::post('newsletters/{newsletter}/enviar', [Admin\NewsletterController::class, 'send'])->name('newsletters.send')->middleware($can('newsletters', 'editar'));

        $crud('users', Admin\UserController::class, 'users');
        Route::get('roles', [Admin\RoleController::class, 'index'])->name('roles.index')->middleware($can('users'));
    });
});

Route::post('contato-enviar', [SiteController::class, 'contact'])->middleware('throttle:5,1')->name('site.contact');
Route::post('inscricao', [SubscriptionController::class, 'store'])->middleware('throttle:5,1')->name('site.subscribe');
Route::get('newsletter/confirmar/{token}', [SubscriptionController::class, 'confirm'])->middleware('throttle:20,1')->name('site.newsletter.confirm');
Route::get('newsletter/cancelar/{token}', [SubscriptionController::class, 'unsubscribeForm'])->middleware('throttle:20,1')->name('site.newsletter.cancel');
Route::post('newsletter/cancelar/{token}', [SubscriptionController::class, 'unsubscribe'])->middleware('throttle:20,1')->name('site.newsletter.unsubscribe');

Route::get('sitemap.xml', [SiteController::class, 'sitemapXml'])->name('site.sitemap');

Route::get('{path?}', [SiteController::class, 'show'])->where('path', '.*')->name('site.page');
