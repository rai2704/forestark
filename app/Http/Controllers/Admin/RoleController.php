<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.users.roles', ['roles' => Role::withCount('users')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.users.role', ['role' => new Role(['permissions' => []])]);
    }

    public function store(Request $request)
    {
        Role::create($this->validated($request));

        return redirect()->route('admin.roles.index')->with('ok', 'Perfil criado.');
    }

    public function edit(Role $role)
    {
        return view('admin.users.role', ['role' => $role]);
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->full_access, 422, 'O perfil de acesso total não pode ser alterado.');
        $role->update($this->validated($request, $role));

        return redirect()->route('admin.roles.index')->with('ok', 'Perfil salvo.');
    }

    public function destroy(Role $role)
    {
        abort_if($role->full_access || $role->users()->exists(), 422, 'Este perfil não pode ser excluído enquanto tiver usuários ou acesso total.');
        $role->delete();

        return back()->with('ok', 'Perfil excluído.');
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('roles', 'name')->ignore($role?->id)],
            'permissions' => 'array',
            'permissions.*' => [Rule::in(array_keys(config('portal.modules')))],
        ]);

        return ['name' => $data['name'], 'permissions' => array_values($data['permissions'] ?? [])];
    }
}
