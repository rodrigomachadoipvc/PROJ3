<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || $user->tipoUtilizador?->nome !== 'Administrador') {
            abort(403, 'Acesso não autorizado. Esta área é exclusiva a administradores.');
        }

        return $next($request);
    }
}
