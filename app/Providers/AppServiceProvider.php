<?php

namespace App\Providers;

use App\Models\Scopes\TenantScope;
use App\Services\TenantService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar TenantService como singleton
        $this->app->singleton(TenantService::class, function ($app) {
            return new TenantService();
        });

        // Registrar PaymentService como singleton
        $this->app->singleton(\App\Services\Payment\PaymentService::class);
        
        // Registrar ReceiptService como singleton
        $this->app->singleton(\App\Services\ReceiptService::class);
        
        // Registrar BulkEmailService como singleton
        $this->app->singleton(\App\Services\BulkEmailService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Aplicar TenantScope globalmente a modelos que tengan congress_id
        Model::addGlobalScope(new TenantScope(
            $this->app->make(TenantService::class)
        ));

        // Optimización: Eager loading por defecto para relaciones comunes
        Model::preventLazyLoading(!app()->isProduction());

        // Optimización: Log de consultas N+1 en desarrollo
        if (app()->isLocal()) {
            DB::listen(function ($query) {
                if ($query->time > 1000) { // Queries que tardan más de 1 segundo
                    Log::warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms',
                    ]);
                }
            });
        }

        // Optimización: Limitar resultados de consultas sin paginación
        Model::preventAccessingMissingAttributes();
    }
}
