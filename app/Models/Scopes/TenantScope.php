<?php

namespace App\Models\Scopes;

use App\Services\TenantService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $congressId = $this->tenantService->getCurrentCongressId();

        // Solo aplicar el scope si hay un congreso activo
        // y el modelo tiene la columna congress_id
        if ($congressId && $this->hasCongressColumn($model)) {
            $builder->where($model->getTable() . '.congress_id', $congressId);
        }
    }

    /**
     * Verificar si el modelo tiene la columna congress_id
     */
    protected function hasCongressColumn(Model $model): bool
    {
        $table = $model->getTable();
        
        // Lista de tablas que NO deben tener el scope (tablas globales)
        $globalTables = [
            'users',
            'congresses',
            'thematic_areas',
            'permissions',
            'roles',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
        ];

        if (in_array($table, $globalTables)) {
            return false;
        }

        // Verificar si la tabla tiene la columna congress_id
        $columns = \Schema::getColumnListing($table);
        return in_array('congress_id', $columns);
    }
}
