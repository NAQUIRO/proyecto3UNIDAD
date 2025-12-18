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
        Schema::create('congress_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congress_id')->constrained('congresses')->cascadeOnDelete();
            $table->string('name'); // Ej: "Inicio de inscripciones", "Fin de recepción de papers"
            $table->text('description')->nullable();
            $table->dateTime('deadline'); // Fecha y hora límite
            $table->boolean('blocks_actions')->default(true); // Si bloquea acciones después de la fecha
            $table->enum('type', ['registration_start', 'registration_end', 'paper_submission_start', 'paper_submission_end', 'review_start', 'review_end', 'event_start', 'event_end', 'custom'])->default('custom');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('congress_milestones');
    }
};
