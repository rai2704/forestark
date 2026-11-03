<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Support\Content;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/** Fotos de um álbum: várias de uma vez, com ordem e opção de aparecer ou não no site. */
class EntryPhotoController extends Controller
{
    public function store(Request $request, string $collection, Entry $entry)
    {
        $this->guard($collection, $entry);
        $files = array_filter((array) $request->file('photos', []));
        if (! $files) {
            throw ValidationException::withMessages(['photos' => 'Selecione ao menos uma foto.']);
        }

        // Valida tudo antes de gravar qualquer arquivo.
        foreach ($files as $file) {
            Content::assertImage($file, 'photos');
        }
        $photos = $this->photos($entry);
        foreach ($files as $file) {
            $photos[] = ['image' => Content::storeImage($file, 'photos'), 'hidden' => false];
        }
        $this->save($entry, $photos);

        return back()->with('ok', count($files).' foto(s) incluída(s).');
    }

    public function toggle(string $collection, Entry $entry, int $index)
    {
        $this->guard($collection, $entry);
        $photos = $this->photos($entry, $index);
        $photos[$index]['hidden'] = empty($photos[$index]['hidden']);
        $this->save($entry, $photos);

        return back();
    }

    public function move(Request $request, string $collection, Entry $entry, int $index)
    {
        $this->guard($collection, $entry);
        $photos = $this->photos($entry, $index);
        $to = $index + ($request->input('direction') === 'up' ? -1 : 1);
        if (isset($photos[$to])) {
            [$photos[$index], $photos[$to]] = [$photos[$to], $photos[$index]];
            $this->save($entry, $photos);
        }

        return back();
    }

    public function destroy(string $collection, Entry $entry, int $index)
    {
        $this->guard($collection, $entry);
        $photos = $this->photos($entry, $index);
        unset($photos[$index]);
        $this->save($entry, array_values($photos));

        return back()->with('ok', 'Foto excluída.');
    }

    private function guard(string $collection, Entry $entry): void
    {
        abort_unless($entry->collection === $collection && ($entry->schema()['photos'] ?? false), 404);
    }

    private function photos(Entry $entry, ?int $index = null): array
    {
        $photos = array_values((array) ($entry->data['photos'] ?? []));
        abort_if($index !== null && ! isset($photos[$index]), 404);

        return $photos;
    }

    private function save(Entry $entry, array $photos): void
    {
        $data = $entry->data ?? [];
        $data['photos'] = $photos;
        $entry->update(['data' => $data]);
    }
}
