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
        return view('admin.users.index', ['users' => User::with('role')->orderBy('name')->get(), 'roles' => Role::withCount('users')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User(['is_active' => true]), 'roles' => Role::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('admin.users.index')->with('ok', 'Usuário criado.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', ['user' => $user, 'roles' => Role::orderBy('name')->get()]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
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
        abort_if($user->is($request->user()), 422, 'Você não pode excluir o próprio usuário.');
        abort_if($user->role?->full_access && $this->fullAccessCount() <= 1, 422, 'É preciso manter ao menos um usuário com acesso total.');
        $user->delete();

        return back()->with('ok', 'Usuário excluído.');
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

        return $data;
    }
}
