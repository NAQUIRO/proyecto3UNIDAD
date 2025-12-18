<?php

namespace App\Services;

use App\Models\Congress;
use Illuminate\Support\Facades\Cache;

class TenantService
{
    protected ?Congress $currentCongress = null;

    /**
     * Obtener el congreso actual basado en la URL o dominio
     */
    public function getCurrentCongress(): ?Congress
    {
        if ($this->currentCongress !== null) {
            return $this->currentCongress;
        }

        $congress = $this->detectCongress();

        if ($congress) {
            $this->currentCongress = $congress;
        }

        return $this->currentCongress;
    }

    /**
     * Detectar el congreso basado en dominio, subdominio o slug en la URL
     */
    protected function detectCongress(): ?Congress
    {
        $request = request();

        // 1. Detectar por dominio personalizado
        $host = $request->getHost();
        $congress = Cache::remember("congress_domain_{$host}", 3600, function () use ($host) {
            return Congress::where('custom_domain', $host)
                ->where('use_custom_domain', true)
                ->where('is_active', true)
                ->first();
        });

        if ($congress) {
            return $congress;
        }

        // 2. Detectar por subdominio
        $subdomain = $this->extractSubdomain($host);
        if ($subdomain) {
            $congress = Cache::remember("congress_subdomain_{$subdomain}", 3600, function () use ($subdomain) {
                return Congress::where('subdomain', $subdomain)
                    ->where('is_active', true)
                    ->first();
            });

            if ($congress) {
                return $congress;
            }
        }

        // 3. Detectar por slug en la ruta (ej: /evento/{slug})
        $slug = $request->route('slug') ?? $request->route('congress');
        if ($slug) {
            $congress = Cache::remember("congress_slug_{$slug}", 3600, function () use ($slug) {
                return Congress::where('slug', $slug)
                    ->where('is_active', true)
                    ->first();
            });

            if ($congress) {
                return $congress;
            }
        }

        return null;
    }

    /**
     * Extraer subdominio del host
     */
    protected function extractSubdomain(string $host): ?string
    {
        $parts = explode('.', $host);
        
        // Si hay más de 2 partes, asumimos que la primera es el subdominio
        // Ejemplo: evento.misistema.com -> evento
        if (count($parts) > 2) {
            return $parts[0];
        }

        return null;
    }

    /**
     * Establecer el congreso actual manualmente
     */
    public function setCurrentCongress(Congress $congress): void
    {
        $this->currentCongress = $congress;
    }

    /**
     * Limpiar el congreso actual (útil para testing o cambio de contexto)
     */
    public function clearCurrentCongress(): void
    {
        $this->currentCongress = null;
    }

    /**
     * Verificar si hay un congreso activo
     */
    public function hasCurrentCongress(): bool
    {
        return $this->getCurrentCongress() !== null;
    }

    /**
     * Obtener el ID del congreso actual
     */
    public function getCurrentCongressId(): ?int
    {
        return $this->getCurrentCongress()?->id;
    }
}

