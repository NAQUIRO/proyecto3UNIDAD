<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class OptimizeProject extends Command
{
    protected $signature = 'project:optimize {--force : Force optimization even in development}';
    protected $description = 'Optimiza completamente el proyecto para mejor rendimiento';

    public function handle()
    {
        $this->info('🚀 Iniciando optimización completa del proyecto...');
        $this->newLine();

        // 1. Limpiar cachés
        $this->info('📦 Limpiando cachés...');
        try {
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('event:clear');
            $this->info('✅ Cachés limpiados');
        } catch (\Exception $e) {
            $this->warn('⚠️  Error al limpiar cachés: ' . $e->getMessage());
        }
        $this->newLine();

        // 2. Verificar storage link
        $this->info('🔗 Verificando storage link...');
        try {
            if (!file_exists(public_path('storage'))) {
                Artisan::call('storage:link');
                $this->info('✅ Storage link creado');
            } else {
                $this->info('✅ Storage link ya existe');
            }
        } catch (\Exception $e) {
            $this->warn('⚠️  Error al crear storage link: ' . $e->getMessage());
        }
        $this->newLine();

        // 3. Cachear configuración
        $this->info('💾 Cacheando configuración...');
        Artisan::call('config:cache');
        $this->info('✅ Configuración cacheada');
        $this->newLine();

        // 4. Cachear rutas
        $this->info('🛣️  Cacheando rutas...');
        try {
            Artisan::call('route:cache');
            $this->info('✅ Rutas cacheadas');
        } catch (\Exception $e) {
            $this->warn('⚠️  No se pudieron cachear rutas: ' . $e->getMessage());
        }
        $this->newLine();

        // 5. Cachear vistas
        $this->info('👁️  Cacheando vistas...');
        Artisan::call('view:cache');
        $this->info('✅ Vistas cacheadas');
        $this->newLine();

        // 6. Optimizar base de datos
        $this->info('🗄️  Optimizando base de datos...');
        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::getDatabaseName();
            $tableKey = 'Tables_in_' . $dbName;
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
            }
            $this->info('✅ Base de datos optimizada');
        } catch (\Exception $e) {
            $this->warn('⚠️  Error al optimizar BD: ' . $e->getMessage());
        }
        $this->newLine();

        // 7. Verificar índices críticos
        $this->info('📊 Verificando índices...');
        $this->checkIndexes();
        $this->newLine();

        // 8. Limpiar logs antiguos
        $this->info('🧹 Limpiando logs antiguos...');
        $this->cleanOldLogs();
        $this->info('✅ Logs limpiados');
        $this->newLine();

        $this->info('✨ Optimización completada exitosamente!');
        $this->newLine();
        $this->info('📝 Próximos pasos recomendados:');
        $this->line('   1. Ejecutar: composer install --optimize-autoloader --no-dev');
        $this->line('   2. Compilar assets: npm run build:prod');
        $this->line('   3. Configurar OPcache en php.ini');
    }

    protected function checkIndexes()
    {
        $criticalIndexes = [
            'congresses' => ['status', 'slug', 'is_active'],
            'papers' => ['congress_id', 'user_id', 'status'],
            'users' => ['email'],
            'payments' => ['user_id', 'congress_id', 'status'],
            'sponsors' => ['congress_id', 'is_active', 'sponsor_type'],
            'speakers' => ['congress_id', 'is_active', 'is_featured'],
        ];

        foreach ($criticalIndexes as $table => $columns) {
            try {
                $indexes = DB::select("SHOW INDEXES FROM `{$table}`");
                $indexColumns = array_map(function ($index) {
                    return $index->Column_name;
                }, $indexes);

                $missing = array_diff($columns, $indexColumns);
                if (empty($missing)) {
                    $this->info("   ✅ {$table}: Índices OK");
                } else {
                    $this->warn("   ⚠️  {$table}: Faltan índices en: " . implode(', ', $missing));
                }
            } catch (\Exception $e) {
                $this->warn("   ⚠️  {$table}: " . $e->getMessage());
            }
        }
    }

    protected function cleanOldLogs()
    {
        $logPath = storage_path('logs');
        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log');
            $cutoff = now()->subDays(30)->timestamp;
            
            $deleted = 0;
            foreach ($files as $file) {
                if (filemtime($file) < $cutoff && filesize($file) > 10485760) { // > 10MB
                    @unlink($file);
                    $deleted++;
                }
            }
            
            if ($deleted > 0) {
                $this->info("   Eliminados {$deleted} archivos de log antiguos");
            }
        }
    }
}

