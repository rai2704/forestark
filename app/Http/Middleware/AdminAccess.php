<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAccess
{
    public function handle(Request $request, Closure $next, string $module = '')
    {
        $user = Auth::user();

        if (! $user || ! $user->is_active) {
            Auth::logout();

            return redirect()->route('admin.login');
        }

        abort_if($module && ! $user->canAccess($module), 403, 'Sem permissão para este módulo.');

        return $next($request);
    }
}
