<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Subscriber;

class MessageController extends Controller
{
    public function index()
    {
        return view('admin.messages.index', ['messages' => ContactMessage::latest()->paginate(20)]);
    }

    public function show(ContactMessage $message)
    {
        $message->update(['read_at' => $message->read_at ?? now()]);

        return view('admin.messages.show', ['message' => $message]);
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('ok', 'Mensagem excluída.');
    }

    public function subscribers()
    {
        return view('admin.messages.subscribers', ['subscribers' => Subscriber::latest()->paginate(50)]);
    }

    public function destroySubscriber(Subscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('ok', 'Inscrito removido.');
    }
}
