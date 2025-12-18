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
        Schema::create('session_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('virtual_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name'); // Nombre del autor (puede ser anónimo)
            $table->string('author_email')->nullable();
            $table->text('content');
            $table->enum('type', ['comment', 'question'])->default('comment');
            $table->foreignId('parent_id')->nullable()->constrained('session_comments')->nullOnDelete(); // Para respuestas
            $table->boolean('is_answered')->default(false); // Si es una pregunta, si fue respondida
            $table->boolean('is_approved')->default(true); // Moderación de comentarios
            $table->integer('likes_count')->default(0);
            $table->timestamps();
            
            $table->index(['session_id', 'parent_id']);
            $table->index(['type', 'is_approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_comments');
    }
};
