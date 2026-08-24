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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number', 30)->unique();
            $table->foreignId('customer_id')
                ->constrained()
                ->restrictOnDelete();
            $table->foreignId('currency_id')
                ->constrained()
                ->restrictOnDelete();
            $table->decimal('principal_amount', 28, 8);
            $table->date('start_date');
            $table->date('maturity_date');
            $table->string('status', 20)
                ->default('active');
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->timestamps();
            $table->index(['customer_id', 'status']);
            $table->index(['currency_id', 'status']);
            $table->index('maturity_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
