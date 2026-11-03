<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\Content;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings', ['groups' => config('portal.settings_groups'), 'data' => SiteSetting::current()->data ?? []]);
    }

    public function update(Request $request)
    {
        $fields = collect(config('portal.settings_groups'))->flatten(1)->all();
        SiteSetting::current()->update(['data' => Content::fromInput($fields, (array) $request->input('data', []), (array) $request->file('uploads', []))]);

        return back()->with('ok', 'Configurações salvas.');
    }
}
