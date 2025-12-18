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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_id')->constrained('papers')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete(); // Revisor
            $table->foreignId('review_assignment_id')->nullable()->constrained('review_assignments')->nullOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('recommendation', ['accept', 'reject', 'revision_required'])->nullable();
            $table->text('comments')->nullable(); // Comentarios generales
            $table->text('confidential_comments')->nullable(); // Comentarios confidenciales (no visibles para el autor)
            $table->decimal('overall_score', 5, 2)->nullable(); // Puntuación general
            $table->boolean('is_blind_review')->default(true); // Revisión a ciegas
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['paper_id', 'reviewer_id']); // Un revisor solo puede revisar un paper una vez
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
