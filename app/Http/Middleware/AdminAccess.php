<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAccess
{
    public function handle(Request $request, Closure $next, string $module = '', string $action = 'acessar')
    {
        $user = Auth::user();

        if (! $user || ! $user->is_active) {
            Auth::logout();

            return redirect()->route('admin.login');
        }

        abort_if($module && ! $user->canAccess($module, $action), 403, 'Sem permissão para esta ação.');

        return $next($request);
    }
}
