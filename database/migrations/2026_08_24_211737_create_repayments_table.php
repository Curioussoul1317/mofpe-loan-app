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
        Schema::create('repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')
                ->constrained()
                ->restrictOnDelete();
            $table->foreignId('currency_id')
                ->constrained()
                ->restrictOnDelete();
            $table->decimal('amount', 28, 8);
            $table->date('payment_date');
            $table->string('reference_number', 100)
                ->unique();
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->timestamps();
            $table->index([
                'loan_id',
                'payment_date',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repayments');
    }
};
