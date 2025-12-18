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
        Schema::create('congress_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congress_id')->constrained('congresses')->cascadeOnDelete();
            $table->string('name'); // Ej: "Early Bird", "Regular", "Late"
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2); // Monto de la tarifa
            $table->string('currency', 3)->default('USD'); // Moneda
            $table->date('start_date'); // Fecha de inicio del período
            $table->date('end_date'); // Fecha de fin del período
            $table->enum('user_type', ['attendee', 'speaker', 'both'])->default('both'); // Tipo de usuario
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('congress_fees');
    }
};
