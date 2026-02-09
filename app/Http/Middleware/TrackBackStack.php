<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackBackStack
{
    private const KEY = 'nav_stack';
    private const MAX = 30;

    // Páginas “raiz” onde o botão deve desaparecer (reset do histórico)
    private const ROOT_ROUTES = [
        'user.dashboard',
        // Se quiseres também no admin, adiciona:
        // 'admin.dashboard',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Se for uma página raiz, reset ao stack (isto resolve o teu bug)
        if ($this->isRootRoute($request) && $this->isTrackableGet($request, $response)) {
            $request->session()->put(self::KEY, [$request->fullUrl()]);
            return $response;
        }

        if (! $this->isTrackableGet($request, $response)) {
            return $response;
        }

        $url = $request->fullUrl();

        $stack = $request->session()->get(self::KEY, []);
        $stack = is_array($stack) ? $stack : [];

        $last = end($stack) ?: null;

        // Evita duplicados consecutivos
        if ($last !== $url) {
            $stack[] = $url;
        }

        // Limitar tamanho
        if (count($stack) > self::MAX) {
            $stack = array_slice($stack, -self::MAX);
        }

        $request->session()->put(self::KEY, $stack);

        return $response;
    }

    private function isRootRoute(Request $request): bool
    {
        return $request->route() && in_array($request->route()->getName(), self::ROOT_ROUTES, true);
    }

    private function isTrackableGet(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        // Ignora AJAX/JSON/Livewire
        if ($request->ajax() || $request->expectsJson() || $request->wantsJson() || $request->header('X-Livewire')) {
            return false;
        }

        // Ignora redirects (evita poluição no stack)
        if (method_exists($response, 'isRedirection') && $response->isRedirection()) {
            return false;
        }

        // Ignora o próprio back e rotas de ficheiros/downloads
        if ($request->routeIs('back') || $request->routeIs('anexos.ver')) {
            return false;
        }

        return true;
    }

    public static function popBackUrl(string $fallback): string
    {
        $stack = session(self::KEY, []);
        $stack = is_array($stack) ? $stack : [];

        if (count($stack) < 2) {
            session()->put(self::KEY, [$fallback]);
            return $fallback;
        }

        // [..., anterior, atual]
        array_pop($stack);              // remove "atual"
        $target = array_pop($stack);    // vai para "anterior"

        if (! $target) {
            session()->put(self::KEY, [$fallback]);
            return $fallback;
        }

        // Mantém o target como “atual”
        $stack[] = $target;
        session()->put(self::KEY, $stack);

        return $target;
    }
}
