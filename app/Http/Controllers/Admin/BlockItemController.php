<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Support\Content;
use Illuminate\Http\Request;

/** Itens de um bloco (banners, perguntas, cards...): cada um tem a própria tela de edição. */
class BlockItemController extends Controller
{
    public function create(Block $block, string $repeater)
    {
        return $this->form($block, $this->field($block, $repeater), null, []);
    }

    public function store(Request $request, Block $block, string $repeater)
    {
        $field = $this->field($block, $repeater);
        $data = $block->data ?? [];
        $data[$repeater] = array_values((array) ($data[$repeater] ?? []));
        $data[$repeater][] = $this->payload($request, $field);
        $block->update(['data' => $data]);

        return redirect()->route('admin.blocks.edit', $block)->with('ok', 'Item incluído.');
    }

    public function edit(Block $block, string $repeater, int $index)
    {
        $field = $this->field($block, $repeater);

        return $this->form($block, $field, $index, $this->items($block, $repeater, $index)[$index]);
    }

    public function update(Request $request, Block $block, string $repeater, int $index)
    {
        $field = $this->field($block, $repeater);
        $items = $this->items($block, $repeater, $index);
        $items[$index] = $this->payload($request, $field);
        $this->save($block, $repeater, $items);

        return redirect()->route('admin.blocks.edit', $block)->with('ok', 'Item salvo.');
    }

    public function move(Request $request, Block $block, string $repeater, int $index)
    {
        $this->field($block, $repeater);
        $items = $this->items($block, $repeater, $index);
        $to = $index + ($request->input('direction') === 'up' ? -1 : 1);

        if (isset($items[$to])) {
            [$items[$index], $items[$to]] = [$items[$to], $items[$index]];
            $this->save($block, $repeater, $items);
        }

        return back();
    }

    public function destroy(Block $block, string $repeater, int $index)
    {
        $this->field($block, $repeater);
        $items = $this->items($block, $repeater, $index);
        unset($items[$index]);
        $this->save($block, $repeater, array_values($items));

        return back()->with('ok', 'Item excluído.');
    }

    private function form(Block $block, array $field, ?int $index, array $item)
    {
        return view('admin.blocks.item', compact('block', 'field', 'index', 'item'));
    }

    private function field(Block $block, string $repeater): array
    {
        $field = collect($block->schema()['fields'])->first(fn ($f) => $f['type'] === 'repeater' && $f['name'] === $repeater);
        abort_unless($field, 404);

        return $field;
    }

    private function items(Block $block, string $repeater, int $index): array
    {
        $items = array_values((array) ($block->data[$repeater] ?? []));
        abort_unless(isset($items[$index]), 404);

        return $items;
    }

    private function save(Block $block, string $repeater, array $items): void
    {
        $data = $block->data ?? [];
        $data[$repeater] = $items;
        $block->update(['data' => $data]);
    }

    private function payload(Request $request, array $field): array
    {
        return Content::fromInput($field['fields'], (array) $request->input('data', []), (array) $request->file('uploads', []));
    }
}
