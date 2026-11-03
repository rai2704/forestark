<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', ['groups' => array_keys(config('portal.settings_groups'))]);
    }

    public function edit(string $group)
    {
        [$title, $fields] = $this->group($group);

        return view('admin.settings.form', ['slug' => $group, 'title' => $title, 'fields' => $fields, 'data' => SiteSetting::current()->data ?? []]);
    }

    public function update(Request $request, string $group)
    {
        [, $fields] = $this->group($group);
        $setting = SiteSetting::current();
        $setting->update(['data' => array_replace($setting->data ?? [], Content::fromInput($fields, (array) $request->input('data', []), (array) $request->file('uploads', [])))]);

        return redirect()->route('admin.settings')->with('ok', 'Configurações salvas.');
    }

    private function group(string $slug): array
    {
        foreach (config('portal.settings_groups') as $title => $fields) {
            if (Str::slug($title) === $slug) {
                return [$title, $fields];
            }
        }
        abort(404);
    }
}
