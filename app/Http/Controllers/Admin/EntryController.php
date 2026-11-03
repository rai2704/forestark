<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Support\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/** CRUD único para todos os módulos de conteúdo definidos em config/portal.php. */
class EntryController extends Controller
{
    public function index(string $collection)
    {
        return view('admin.entries.index', [
            'collection' => $collection,
            'def' => Entry::definition($collection),
            'entries' => Entry::of($collection)->paginate(30),
        ]);
    }

    public function create(string $collection)
    {
        return $this->form($collection, new Entry(['collection' => $collection, 'data' => [], 'is_published' => true, 'published_at' => now()]));
    }

    public function store(Request $request, string $collection)
    {
        $entry = new Entry(['collection' => $collection, 'position' => (int) Entry::where('collection', $collection)->max('position') + 1]);
        $this->fill($request, $entry);

        if (Entry::definition($collection)['photos'] ?? false) {
            return redirect()->route('admin.entries.edit', [$collection, $entry])->with('ok', 'Álbum criado. Agora envie as fotos.');
        }

        return redirect()->route('admin.entries.index', $collection)->with('ok', Entry::definition($collection)['singular'].' incluído(a).');
    }

    public function edit(string $collection, Entry $entry)
    {
        $this->guard($collection, $entry);

        return $this->form($collection, $entry);
    }

    public function update(Request $request, string $collection, Entry $entry)
    {
        $this->guard($collection, $entry);
        $this->fill($request, $entry);

        return redirect()->route('admin.entries.index', $collection)->with('ok', Entry::definition($collection)['singular'].' salvo(a).');
    }

    public function toggle(string $collection, Entry $entry)
    {
        $this->guard($collection, $entry);
        $entry->update(['is_published' => ! $entry->is_published]);

        return back();
    }

    public function move(Request $request, string $collection, Entry $entry)
    {
        $this->guard($collection, $entry);
        $list = Entry::of($collection)->get()->values()->all();
        $from = collect($list)->search(fn ($e) => $e->id === $entry->id);
        $to = $from + ($request->input('direction') === 'up' ? -1 : 1);

        if (isset($list[$to])) {
            [$list[$from], $list[$to]] = [$list[$to], $list[$from]];
            foreach ($list as $position => $e) {
                $e->update(['position' => $position]);
            }
        }

        return back();
    }

    public function destroy(string $collection, Entry $entry)
    {
        $this->guard($collection, $entry);
        $entry->delete();

        return back()->with('ok', 'Registro excluído.');
    }

    private function form(string $collection, Entry $entry)
    {
        return view('admin.entries.form', ['collection' => $collection, 'def' => Entry::definition($collection), 'entry' => $entry]);
    }

    private function guard(string $collection, Entry $entry): void
    {
        abort_unless($entry->collection === $collection, 404);
    }

    private function fill(Request $request, Entry $entry): void
    {
        $def = Entry::definition($entry->collection);
        $photos = $entry->data['photos'] ?? null;
        $entry->data = Content::fromInput($def['fields'], (array) $request->input('data', []), (array) $request->file('uploads', []));
        if ($def['photos'] ?? false) {
            $entry->data = $entry->data + ['photos' => $photos ?? []];
        }
        $entry->is_published = $request->boolean('is_published');

        if ($def['dated'] ?? false) {
            $date = $request->validate(['published_at' => 'nullable|date'])['published_at'] ?? null;
            $entry->published_at = $date ? Carbon::parse($date) : ($entry->published_at ?? now());
        }
        if ($def['detail'] ?? false) {
            $wanted = trim((string) $request->input('slug')) ?: ($entry->slug ?: $entry->slugSource());
            $entry->slug = Entry::uniqueSlug($entry->collection, $wanted, $entry->id);
        }
        $entry->save();
    }
}
