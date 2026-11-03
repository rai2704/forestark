<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class RoleController extends Controller
{
    /** Matriz de permissões: módulos × usuários. */
    public function index()
    {
        return view('admin.users.roles', [
            'users' => User::with('role')->orderBy('name')->get(),
            'groups' => collect(config('portal.modules'))->groupBy('group', preserveKeys: true),
        ]);
    }
}
