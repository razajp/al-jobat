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
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->date('date');
            $table->string('type');
            $table->string('method');
            $table->integer('amount');
            $table->string('cheque_no')->nullable()->unique();
            $table->string('slip_no')->nullable()->unique();
            $table->string('reff_no')->nullable()->unique();
            $table->string('transaction_id')->nullable();
            $table->date('cheque_date')->nullable();
            $table->date('slip_date')->nullable();
            $table->date('clear_date')->nullable();
            $table->foreignId('bank_id')->nullable()->constrained('setups')->onDelete('cascade');
            $table->string('remarks')->nullable();
            $table->foreignId('program_id')->nullable()->constrained('payment_programs')->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->onDelete('cascade');
            $table->boolean('is_return')->default(false);
            $table->foreignId('d_r_id')->nullable()->constrained('d_r_s')->onDelete('cascade');

            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_payments');
    }
};
