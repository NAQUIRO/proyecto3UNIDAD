<?php

namespace App\Http\Middleware;

use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $congress = $this->tenantService->getCurrentCongress();

        // Si no se encuentra un congreso y estamos en una ruta que lo requiere, abortar
        if (!$congress && $this->requiresCongress($request)) {
            abort(404, 'Congreso no encontrado');
        }

        // Establecer el congreso actual en el servicio
        if ($congress) {
            $this->tenantService->setCurrentCongress($congress);
            
            // Agregar el congreso al request para fácil acceso
            $request->merge(['current_congress' => $congress]);
        }

        return $next($request);
    }

    /**
     * Determinar si la ruta requiere un congreso
     */
    protected function requiresCongress(Request $request): bool
    {
        $path = $request->path();
        
        // Rutas que requieren congreso (prefijo /evento/{slug})
        $tenantRoutes = [
            'evento',
            'congress',
        ];

        foreach ($tenantRoutes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
