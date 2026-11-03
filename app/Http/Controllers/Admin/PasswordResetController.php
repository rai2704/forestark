<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function request()
    {
        return view('admin.recuperar-senha');
    }

    /** A resposta é sempre igual, para não revelar quais e-mails estão cadastrados. */
    public function email(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);
        $user = User::where('email', $data['email'])->where('is_active', true)->first();

        if ($user) {
            $link = route('admin.password.reset', ['token' => Password::createToken($user), 'email' => $user->email]);
            try {
                Mail::raw('Recebemos um pedido para redefinir sua senha no painel do site '.Site::settings()['site_name'].".\n\nAcesse o link abaixo, válido por 60 minutos:\n{$link}\n\nSe você não pediu, ignore este e-mail.", function ($mail) use ($user) {
                    $mail->to($user->email)->subject('Redefinição de senha');
                });
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('ok', 'Se esse e-mail estiver cadastrado e ativo, enviamos um link de redefinição para ele.');
    }

    public function edit(Request $request, string $token)
    {
        return view('admin.redefinir-senha', ['token' => $token, 'email' => $request->query('email', '')]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:10|confirmed',
        ]);

        $status = Password::reset($data + ['password_confirmation' => $request->input('password_confirmation')], function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('ok', 'Senha redefinida. Faça login.')
            : back()->withErrors(['email' => 'Link inválido ou expirado. Solicite um novo.']);
    }
}
