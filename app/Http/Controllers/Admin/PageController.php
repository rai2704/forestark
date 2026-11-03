<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\Content;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::withCount('blocks')->orderBy('slug')->get()]);
    }

    public function blocks(Page $page)
    {
        return view('admin.pages.blocks', ['page' => $page->load('blocks')]);
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page(['data' => [], 'is_published' => true])]);
    }

    public function store(Request $request)
    {
        $page = Page::create($this->payload($request));

        return redirect()->route('admin.pages.blocks', $page)->with('ok', 'Página criada. Agora adicione os blocos.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', ['page' => $page]);
    }

    public function update(Request $request, Page $page)
    {
        $page->update($this->payload($request, $page));

        return redirect()->route('admin.pages.index')->with('ok', 'Página salva.');
    }

    public function destroy(Page $page)
    {
        abort_if($page->slug === '', 422, 'A página inicial não pode ser excluída.');
        $page->delete();

        return redirect()->route('admin.pages.index')->with('ok', 'Página excluída.');
    }

    private function payload(Request $request, ?Page $page = null): array
    {
        $isHome = $page?->slug === '';
        $slug = $isHome ? '' : $request->validate([
            'slug' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', Rule::notIn(['admin', 'uploads', 'build', 'contato-enviar', 'inscricao', 'up', 'mapa-do-site', 'flags', 'newsletter']), Rule::unique('pages', 'slug')->ignore($page?->id)],
        ])['slug'];

        return [
            'slug' => $slug,
            'is_published' => $isHome || $request->boolean('is_published'),
            'data' => Content::fromInput(config('portal.page_fields'), (array) $request->input('data', [])),
        ];
    }
}
