<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\AdminAccess;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [Admin\AuthController::class, 'login'])->middleware('throttle:20,1');
    Route::get('recuperar-senha', [Admin\PasswordResetController::class, 'request'])->name('password.request');
    Route::post('recuperar-senha', [Admin\PasswordResetController::class, 'email'])->middleware('throttle:5,1');
    Route::get('redefinir-senha/{token}', [Admin\PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('redefinir-senha', [Admin\PasswordResetController::class, 'update'])->middleware('throttle:5,1')->name('password.update');

    Route::middleware(['auth', AdminAccess::class])->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', Admin\DashboardController::class)->name('home');
        Route::get('perfil', [Admin\AuthController::class, 'profile'])->name('profile');
        Route::put('perfil', [Admin\AuthController::class, 'updateProfile']);

        Route::middleware(AdminAccess::class.':pages')->group(function () {
            Route::resource('pages', Admin\PageController::class)->except('show');
            Route::post('pages/{page}/blocks', [Admin\BlockController::class, 'store'])->name('blocks.store');
            Route::get('blocks/{block}/edit', [Admin\BlockController::class, 'edit'])->name('blocks.edit');
            Route::put('blocks/{block}', [Admin\BlockController::class, 'update'])->name('blocks.update');
            Route::post('blocks/{block}/move', [Admin\BlockController::class, 'move'])->name('blocks.move');
            Route::post('blocks/{block}/toggle', [Admin\BlockController::class, 'toggle'])->name('blocks.toggle');
            Route::post('blocks/{block}/duplicate', [Admin\BlockController::class, 'duplicate'])->name('blocks.duplicate');
            Route::delete('blocks/{block}', [Admin\BlockController::class, 'destroy'])->name('blocks.destroy');
        });

        Route::middleware(AdminAccess::class.':menus')->group(function () {
            Route::resource('menus', Admin\MenuController::class)->except('show');
            Route::post('menus/{menu}/move', [Admin\MenuController::class, 'move'])->name('menus.move');
        });

        Route::middleware(AdminAccess::class.':languages')->group(function () {
            Route::resource('languages', Admin\LanguageController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        Route::middleware(AdminAccess::class.':settings')->group(function () {
            Route::get('configuracoes', [Admin\SettingController::class, 'edit'])->name('settings');
            Route::put('configuracoes', [Admin\SettingController::class, 'update']);
        });

        Route::middleware(AdminAccess::class.':messages')->group(function () {
            Route::resource('messages', Admin\MessageController::class)->only(['index', 'show', 'destroy']);
            Route::get('inscritos', [Admin\MessageController::class, 'subscribers'])->name('subscribers');
            Route::delete('inscritos/{subscriber}', [Admin\MessageController::class, 'destroySubscriber'])->name('subscribers.destroy');
            Route::resource('newsletters', Admin\NewsletterController::class)->except('show');
            Route::post('newsletters/{newsletter}/enviar', [Admin\NewsletterController::class, 'send'])->name('newsletters.send');
        });

        Route::middleware(AdminAccess::class.':users')->group(function () {
            Route::resource('users', Admin\UserController::class)->except('show');
            Route::resource('roles', Admin\RoleController::class)->except('show');
        });
    });
});

Route::post('contato-enviar', [SiteController::class, 'contact'])->middleware('throttle:5,1')->name('site.contact');
Route::post('inscricao', [SubscriptionController::class, 'store'])->middleware('throttle:5,1')->name('site.subscribe');
Route::get('newsletter/confirmar/{token}', [SubscriptionController::class, 'confirm'])->middleware('throttle:20,1')->name('site.newsletter.confirm');
Route::get('newsletter/cancelar/{token}', [SubscriptionController::class, 'unsubscribeForm'])->middleware('throttle:20,1')->name('site.newsletter.cancel');
Route::post('newsletter/cancelar/{token}', [SubscriptionController::class, 'unsubscribe'])->middleware('throttle:20,1')->name('site.newsletter.unsubscribe');

Route::get('sitemap.xml', [SiteController::class, 'sitemapXml'])->name('site.sitemap');

Route::get('{path?}', [SiteController::class, 'show'])->where('path', '.*')->name('site.page');
