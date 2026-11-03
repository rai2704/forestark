<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', ['users' => User::with('role')->orderBy('name')->get()]);
    }

    public function create()
    {
        return $this->form(new User(['is_active' => true, 'permissions' => []]));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->guardEscalation($request, null, $data['role_id']);
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('admin.users.index')->with('ok', 'Usuário criado.');
    }

    public function edit(Request $request, User $user)
    {
        $this->guardTarget($request, $user);

        return $this->form($user);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        $this->guardEscalation($request, $user, $data['role_id']);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $losesFullAccess = ! Role::find($data['role_id'])?->full_access || ! $data['is_active'];
        abort_if($user->role?->full_access && $losesFullAccess && $this->fullAccessCount() <= 1, 422, 'É preciso manter ao menos um usuário com acesso total.');
        $user->update($data);

        return redirect()->route('admin.users.index')->with('ok', 'Usuário salvo.');
    }

    public function destroy(Request $request, User $user)
    {
        $this->guardTarget($request, $user);
        abort_if($user->is($request->user()), 422, 'Você não pode excluir o próprio usuário.');
        abort_if($user->role?->full_access && $this->fullAccessCount() <= 1, 422, 'É preciso manter ao menos um usuário com acesso total.');
        $user->delete();

        return back()->with('ok', 'Usuário excluído.');
    }

    private function form(User $user)
    {
        return view('admin.users.form', ['user' => $user, 'roles' => Role::orderBy('id')->get()]);
    }

    /** Sem acesso total, ninguém altera a si mesmo nem quem tem acesso total. */
    private function guardTarget(Request $request, User $user): void
    {
        if ($request->user()->role?->full_access) {
            return;
        }
        abort_if($user->is($request->user()), 403, 'Use "Meu Perfil" para alterar seus dados. Suas permissões só podem ser alteradas por um administrador.');
        abort_if($user->role?->full_access, 403, 'Somente quem tem acesso total pode alterar este usuário.');
    }

    private function guardEscalation(Request $request, ?User $user, int|string $roleId): void
    {
        if ($user) {
            $this->guardTarget($request, $user);
        }
        abort_if(! $request->user()->role?->full_access && Role::find($roleId)?->full_access, 403, 'Somente quem tem acesso total pode conceder este nível.');
    }

    private function fullAccessCount(): int
    {
        return User::where('is_active', true)->whereHas('role', fn ($q) => $q->where('full_access', true))->count();
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user?->id)],
            'role_id' => 'required|exists:roles,id',
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:10', 'confirmed'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $data['permissions'] = [];
        if (! Role::find($data['role_id'])?->full_access) {
            foreach (array_keys(config('portal.modules')) as $module) {
                foreach (array_keys(config('portal.actions')) as $action) {
                    if ($request->boolean("perm.$module.$action")) {
                        $data['permissions'][$module][$action] = true;
                    }
                }
            }
        }

        return $data;
    }
}
