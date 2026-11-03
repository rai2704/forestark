<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LanguageController extends Controller
{
    private const FLAG_RULE = ['required', 'string', 'size:2', 'regex:/^[a-z]{2}$/'];

    public function index()
    {
        return view('admin.languages', ['languages' => Language::orderBy('position')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'regex:/^[a-z]{2}(-[A-Z]{2})?$/', 'unique:languages,code', Rule::notIn(['admin', 'up'])],
            'name' => 'required|string|max:50',
            'flag' => self::FLAG_RULE,
        ]);
        abort_if(Page::where('slug', $data['code'])->exists(), 422, 'Já existe uma página com esse endereço.');
        Language::create($data + ['position' => (int) Language::max('position') + 1, 'is_active' => true]);

        return back()->with('ok', 'Idioma adicionado. Os textos sem tradução aparecem no idioma padrão.');
    }

    public function update(Request $request, Language $language)
    {
        $data = $request->validate(['name' => 'required|string|max:50', 'flag' => self::FLAG_RULE, 'position' => 'required|integer|min:0']);
        $data['is_active'] = $language->is_default || $request->boolean('is_active');
        $language->update($data);

        return back()->with('ok', 'Idioma salvo.');
    }

    public function destroy(Language $language)
    {
        abort_if($language->is_default, 422, 'O idioma padrão não pode ser excluído.');
        $language->delete();

        return back()->with('ok', 'Idioma excluído. As traduções ficam guardadas até o próximo salvamento de cada conteúdo.');
    }
}
