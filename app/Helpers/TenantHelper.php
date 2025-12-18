<?php

if (!function_exists('currentCongress')) {
    /**
     * Obtener el congreso actual
     */
    function currentCongress(): ?\App\Models\Congress
    {
        return app(\App\Services\TenantService::class)->getCurrentCongress();
    }
}

if (!function_exists('currentCongressId')) {
    /**
     * Obtener el ID del congreso actual
     */
    function currentCongressId(): ?int
    {
        return app(\App\Services\TenantService::class)->getCurrentCongressId();
    }
}

if (!function_exists('hasCurrentCongress')) {
    /**
     * Verificar si hay un congreso activo
     */
    function hasCurrentCongress(): bool
    {
        return app(\App\Services\TenantService::class)->hasCurrentCongress();
    }
}

