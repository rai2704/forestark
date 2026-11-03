<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Subscriber;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.home', ['stats' => [
            'Páginas' => Page::count(),
            'Blocos' => Block::count(),
            'Mensagens não lidas' => ContactMessage::whereNull('read_at')->count(),
            'Inscritos' => Subscriber::count(),
        ]]);
    }
}
