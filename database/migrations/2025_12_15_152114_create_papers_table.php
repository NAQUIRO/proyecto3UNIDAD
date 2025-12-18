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
        Schema::create('papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congress_id')->constrained('congresses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Autor principal
            $table->foreignId('thematic_area_id')->constrained('thematic_areas')->cascadeOnDelete();
            $table->foreignId('editorial_id')->nullable()->constrained('editorials')->nullOnDelete();
            $table->string('title');
            $table->text('abstract'); // Resumen
            $table->text('keywords')->nullable(); // Palabras clave separadas por comas
            $table->integer('word_count')->default(0); // Contador de palabras
            $table->integer('word_limit')->default(500); // Límite de palabras
            $table->enum('status', ['draft', 'submitted', 'under_review', 'accepted', 'rejected', 'revision_required'])->default('draft');
            $table->enum('review_status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->boolean('plagiarism_suspected')->default(false); // Marca de sospecha de plagio
            $table->text('plagiarism_notes')->nullable(); // Notas sobre plagio
            $table->text('revision_notes')->nullable(); // Notas de revisión
            $table->text('rejection_reason')->nullable(); // Razón de rechazo
            $table->string('video_url')->nullable(); // Enlace de video para defensa
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('papers');
    }
};
