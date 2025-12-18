<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congress_id')->nullable()->constrained('congresses')->nullOnDelete();
            $table->string('name'); // Nombre de la plantilla
            $table->enum('type', ['attendance', 'presentation', 'participation'])->default('attendance');
            $table->text('description')->nullable();
            $table->text('html_template')->nullable(); // Plantilla HTML para el certificado
            $table->string('background_image')->nullable(); // Imagen de fondo
            $table->json('fields')->nullable(); // Campos dinámicos (nombre, fecha, etc.)
            $table->json('settings')->nullable(); // Configuraciones (fuentes, colores, etc.)
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
