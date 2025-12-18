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
        Schema::create('paper_coauthors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_id')->constrained('papers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Si está registrado
            $table->string('name'); // Nombre del coautor
            $table->string('email')->nullable(); // Email del coautor (si no está registrado)
            $table->string('affiliation')->nullable(); // Afiliación institucional
            $table->integer('order')->default(1); // Orden de autoría (1, 2, 3...)
            $table->boolean('is_registered_user')->default(false); // Si es usuario registrado
            $table->timestamps();
            
            $table->unique(['paper_id', 'user_id']); // Un usuario solo puede ser coautor una vez por paper
            $table->index(['paper_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_coauthors');
    }
};
