<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Language;
use App\Models\Page;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $u = $request->user();
        $link = fn (string $module, string $route) => $u->canAccess($module) ? route($route) : '#';

        return view('admin.home', [
            'stats' => [
                ['label' => 'Páginas', 'value' => Page::count(), 'icon' => 'fa-file-alt', 'url' => $link('pages', 'admin.pages.index')],
                ['label' => 'Msgs Não Lidas', 'value' => ContactMessage::whereNull('read_at')->count(), 'icon' => 'fa-envelope', 'url' => $link('messages', 'admin.messages.index')],
                ['label' => 'Inscritos', 'value' => Subscriber::count(), 'icon' => 'fa-user-plus', 'url' => $link('subscribers', 'admin.subscribers')],
                ['label' => 'Idiomas', 'value' => Language::where('is_active', true)->count(), 'icon' => 'fa-language', 'url' => $link('languages', 'admin.languages.index')],
            ],
            'recent' => $u->canAccess('messages') ? ContactMessage::latest()->limit(5)->get() : null,
        ]);
    }
}
