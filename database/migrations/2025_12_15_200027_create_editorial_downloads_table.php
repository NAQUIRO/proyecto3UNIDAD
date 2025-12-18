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
        Schema::create('editorial_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congress_id')->constrained('congresses')->cascadeOnDelete();
            $table->foreignId('editorial_id')->constrained('editorials')->cascadeOnDelete();
            $table->foreignId('downloaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('zip_path')->nullable(); // Ruta del archivo ZIP generado
            $table->integer('files_count')->default(0); // Número de archivos incluidos
            $table->integer('total_size')->default(0); // Tamaño total en bytes
            $table->enum('status', ['generating', 'ready', 'downloaded', 'expired'])->default('generating');
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Fecha de expiración del enlace
            $table->timestamps();
            
            $table->index(['congress_id', 'editorial_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editorial_downloads');
    }
};
