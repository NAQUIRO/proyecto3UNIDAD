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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congress_id')->constrained('congresses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('certificate_templates')->nullOnDelete();
            $table->enum('type', ['attendance', 'presentation', 'participation'])->default('attendance');
            $table->string('certificate_number')->unique(); // Número único del certificado
            $table->string('pdf_path')->nullable(); // Ruta del PDF generado
            $table->enum('status', ['pending', 'generated', 'issued', 'revoked'])->default('pending');
            $table->text('validation_notes')->nullable(); // Notas sobre la validación
            $table->json('requirements_met')->nullable(); // Requisitos cumplidos
            $table->json('requirements_failed')->nullable(); // Requisitos no cumplidos
            $table->boolean('is_valid')->default(true);
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
            
            $table->index(['congress_id', 'user_id', 'type']);
            $table->index('certificate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
