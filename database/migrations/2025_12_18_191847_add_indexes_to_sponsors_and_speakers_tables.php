<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar índices a sponsors si la tabla existe
        if (Schema::hasTable('sponsors')) {
            Schema::table('sponsors', function (Blueprint $table) {
                try {
                    if (!$this->indexExists('sponsors', 'sponsors_congress_id_index')) {
                        $table->index('congress_id', 'sponsors_congress_id_index');
                    }
                } catch (\Exception $e) {
                    // Índice ya existe o error
                }
                
                try {
                    if (!$this->indexExists('sponsors', 'sponsors_is_active_index')) {
                        $table->index('is_active', 'sponsors_is_active_index');
                    }
                } catch (\Exception $e) {
                    // Índice ya existe o error
                }
                
                try {
                    if (!$this->indexExists('sponsors', 'sponsors_sponsor_type_index')) {
                        $table->index('sponsor_type', 'sponsors_sponsor_type_index');
                    }
                } catch (\Exception $e) {
                    // Índice ya existe o error
                }
            });
        }

        // Agregar índices a speakers si la tabla existe
        if (Schema::hasTable('speakers')) {
            Schema::table('speakers', function (Blueprint $table) {
                try {
                    if (!$this->indexExists('speakers', 'speakers_congress_id_index')) {
                        $table->index('congress_id', 'speakers_congress_id_index');
                    }
                } catch (\Exception $e) {
                    // Índice ya existe o error
                }
                
                try {
                    if (!$this->indexExists('speakers', 'speakers_is_active_index')) {
                        $table->index('is_active', 'speakers_is_active_index');
                    }
                } catch (\Exception $e) {
                    // Índice ya existe o error
                }
                
                try {
                    if (!$this->indexExists('speakers', 'speakers_is_featured_index')) {
                        $table->index('is_featured', 'speakers_is_featured_index');
                    }
                } catch (\Exception $e) {
                    // Índice ya existe o error
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sponsors')) {
            Schema::table('sponsors', function (Blueprint $table) {
                try {
                    $table->dropIndex('sponsors_congress_id_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('sponsors_is_active_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('sponsors_sponsor_type_index');
                } catch (\Exception $e) {}
            });
        }

        if (Schema::hasTable('speakers')) {
            Schema::table('speakers', function (Blueprint $table) {
                try {
                    $table->dropIndex('speakers_congress_id_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('speakers_is_active_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('speakers_is_featured_index');
                } catch (\Exception $e) {}
            });
        }
    }

    /**
     * Verificar si un índice existe
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($result) > 0;
    }
};
