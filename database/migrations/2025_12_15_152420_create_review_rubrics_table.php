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
        Schema::create('review_rubrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->string('criterion'); // Criterio de evaluación (ej: "Originalidad", "Metodología")
            $table->text('description')->nullable(); // Descripción del criterio
            $table->decimal('score', 5, 2); // Puntuación (ej: 0-10 o 0-5)
            $table->decimal('max_score', 5, 2)->default(10); // Puntuación máxima
            $table->text('comments')->nullable(); // Comentarios sobre este criterio
            $table->integer('order')->default(0); // Orden de visualización
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_rubrics');
    }
};
