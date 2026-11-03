<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Auth::check() ? redirect()->route('admin.home') : view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $key = 'admin-login:'.Str::lower($data['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => 'Muitas tentativas. Aguarde '.RateLimiter::availableIn($key).' segundos.'])->onlyInput('email');
        }

        if (! Auth::attempt($data + ['is_active' => true])) {
            RateLimiter::hit($key, 300);

            return back()->withErrors(['email' => 'E-mail ou senha incorretos.'])->onlyInput('email');
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.home'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:190|unique:users,email,'.$user->id,
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|string|min:10|confirmed',
        ]);
        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return back()->with('ok', 'Perfil atualizado.');
    }
}
