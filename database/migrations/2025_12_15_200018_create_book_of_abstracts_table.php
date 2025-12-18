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
        Schema::create('book_of_abstracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congress_id')->constrained('congresses')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title')->default('Libro de Resúmenes');
            $table->text('description')->nullable();
            $table->string('pdf_path')->nullable(); // Ruta del PDF generado
            $table->string('cover_image')->nullable(); // Imagen de portada
            $table->enum('status', ['draft', 'generating', 'completed', 'published'])->default('draft');
            $table->integer('total_papers')->default(0); // Total de papers incluidos
            $table->json('included_papers')->nullable(); // IDs de papers incluidos
            $table->json('settings')->nullable(); // Configuraciones de formato
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_of_abstracts');
    }
};
