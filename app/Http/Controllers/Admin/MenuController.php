<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Support\Content;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public const LOCATIONS = ['header' => 'Menu principal', 'footer' => 'Links do rodapé'];

    public function index()
    {
        return view('admin.menus.index', ['items' => MenuItem::orderBy('position')->get()->groupBy('location'), 'locations' => self::LOCATIONS]);
    }

    public function create(Request $request)
    {
        return view('admin.menus.form', ['item' => new MenuItem(['location' => $request->query('location', 'header'), 'data' => [], 'is_visible' => true])]);
    }

    public function store(Request $request)
    {
        $location = $request->validate(['location' => ['required', Rule::in(array_keys(self::LOCATIONS))]])['location'];
        MenuItem::create($this->payload($request) + ['location' => $location, 'position' => (int) MenuItem::where('location', $location)->max('position') + 1]);

        return redirect()->route('admin.menus.index')->with('ok', 'Item criado.');
    }

    public function edit(MenuItem $menu)
    {
        return view('admin.menus.form', ['item' => $menu]);
    }

    public function update(Request $request, MenuItem $menu)
    {
        $menu->update($this->payload($request));

        return redirect()->route('admin.menus.index')->with('ok', 'Item salvo.');
    }

    public function move(Request $request, MenuItem $menu)
    {
        $items = MenuItem::where('location', $menu->location)->orderBy('position')->get()->values();
        $from = $items->search(fn ($i) => $i->id === $menu->id);
        $to = $from + ($request->input('direction') === 'up' ? -1 : 1);

        if ($items->has($to)) {
            $list = $items->all();
            [$list[$from], $list[$to]] = [$list[$to], $list[$from]];
            foreach ($list as $position => $i) {
                $i->update(['position' => $position]);
            }
        }

        return back();
    }

    public function destroy(MenuItem $menu)
    {
        $menu->delete();

        return back()->with('ok', 'Item excluído.');
    }

    private function payload(Request $request): array
    {
        return [
            'data' => Content::fromInput(config('portal.menu_fields'), (array) $request->input('data', [])),
            'is_visible' => $request->boolean('is_visible'),
        ];
    }
}
