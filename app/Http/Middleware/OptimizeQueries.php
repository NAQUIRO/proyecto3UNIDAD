<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class OptimizeQueries
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Agregar headers de cache para assets estáticos
        if ($request->is('css/*') || $request->is('js/*') || $request->is('images/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        }

        // Agregar headers de compresión
        if (function_exists('gzencode') && !$response->headers->has('Content-Encoding')) {
            $content = $response->getContent();
            if (strlen($content) > 1024) { // Solo comprimir si es mayor a 1KB
                $response->setContent(gzencode($content, 6));
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Vary', 'Accept-Encoding');
            }
        }

        return $response;
    }
}

