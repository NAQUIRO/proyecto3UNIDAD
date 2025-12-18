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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('congress_id')->nullable()->constrained('congresses')->nullOnDelete();
            $table->string('transaction_type'); // registration, refund, adjustment, etc.
            $table->enum('type', ['debit', 'credit']); // Débito o crédito
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->text('description')->nullable();
            $table->string('reference')->nullable(); // Referencia externa
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['congress_id', 'created_at']);
            $table->index('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
