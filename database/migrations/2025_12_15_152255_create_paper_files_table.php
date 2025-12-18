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
        Schema::create('paper_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_id')->constrained('papers')->cascadeOnDelete();
            $table->string('file_path'); // Ruta del archivo
            $table->string('file_name'); // Nombre original del archivo
            $table->string('file_type'); // Tipo: abstract, full_paper, presentation, etc.
            $table->string('mime_type')->nullable();
            $table->integer('file_size')->nullable(); // Tamaño en bytes
            $table->enum('version', ['draft', 'final', 'revised'])->default('draft');
            $table->integer('version_number')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_files');
    }
};
