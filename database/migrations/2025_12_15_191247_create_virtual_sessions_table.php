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
        Schema::create('virtual_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congress_id')->constrained('congresses')->cascadeOnDelete();
            $table->foreignId('symposium_id')->nullable()->constrained('symposia')->nullOnDelete();
            $table->foreignId('paper_id')->nullable()->constrained('papers')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url'); // URL del video (YouTube, Vimeo, etc.)
            $table->string('video_provider')->default('youtube'); // youtube, vimeo, custom
            $table->string('video_id')->nullable(); // ID del video en el proveedor
            $table->integer('duration_minutes')->nullable(); // Duración en minutos
            $table->dateTime('scheduled_at')->nullable(); // Fecha y hora programada
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
            $table->integer('views_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->boolean('is_live')->default(false);
            $table->boolean('is_recorded')->default(false);
            $table->text('presenter_notes')->nullable(); // Notas del presentador
            $table->timestamps();
            
            $table->index(['congress_id', 'symposium_id']);
            $table->index(['status', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_sessions');
    }
};
