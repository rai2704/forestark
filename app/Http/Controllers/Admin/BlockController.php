<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Page;
use App\Support\Content;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlockController extends Controller
{
    public function store(Request $request, Page $page)
    {
        $type = $request->validate(['type' => ['required', Rule::in(array_keys(config('portal.blocks')))]])['type'];
        $block = $page->blocks()->create(['type' => $type, 'data' => [], 'position' => (int) $page->blocks()->max('position') + 1]);

        return redirect()->route('admin.blocks.edit', $block);
    }

    public function edit(Block $block)
    {
        $fields = collect($block->schema()['fields']);

        return view('admin.blocks.form', [
            'block' => $block,
            'scalars' => $fields->where('type', '!=', 'repeater')->values()->all(),
            'repeaters' => $fields->where('type', 'repeater')->values()->all(),
        ]);
    }

    public function update(Request $request, Block $block)
    {
        $data = $block->data ?? [];

        if ($request->has('data')) {
            $scalars = collect($block->schema()['fields'])->where('type', '!=', 'repeater')->values()->all();
            $data = array_replace($data, Content::fromInput($scalars, (array) $request->input('data', []), (array) $request->file('uploads', [])));
        }
        if ($request->has('source')) {
            $source = $request->validate(['source' => ['nullable', Rule::in($block->schema()['sources'] ?? [])]])['source'] ?? null;
            unset($data['source']);
            if ($source) {
                $data['source'] = $source;
            }
        }
        $block->update(['data' => $data]);

        return back()->with('ok', 'Bloco salvo.');
    }

    public function move(Request $request, Block $block)
    {
        $blocks = $block->page->blocks()->get()->values();
        $from = $blocks->search(fn ($b) => $b->id === $block->id);
        $to = $from + ($request->input('direction') === 'up' ? -1 : 1);

        if ($blocks->has($to)) {
            $list = $blocks->all();
            [$list[$from], $list[$to]] = [$list[$to], $list[$from]];
            foreach ($list as $position => $b) {
                $b->update(['position' => $position]);
            }
        }

        return back();
    }

    public function toggle(Block $block)
    {
        $block->update(['is_visible' => ! $block->is_visible]);

        return back();
    }

    public function duplicate(Block $block)
    {
        $copy = $block->replicate();
        $copy->position = (int) $block->page->blocks()->max('position') + 1;
        $copy->save();

        return back()->with('ok', 'Bloco duplicado no fim da página.');
    }

    public function destroy(Block $block)
    {
        $block->delete();

        return back()->with('ok', 'Bloco excluído.');
    }
}
