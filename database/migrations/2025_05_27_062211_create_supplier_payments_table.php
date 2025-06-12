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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->date('date');
            $table->string('method');
            $table->integer('amount');
            $table->string('transaction_id')->nullable();
            $table->foreignId('cheque_id')->nullable()->constrained('customer_payments')->onDelete('cascade');
            $table->foreignId('slip_id')->nullable()->constrained('customer_payments')->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->onDelete('cascade');
            $table->foreignId('program_id')->nullable()->constrained('payment_programs')->onDelete('cascade');
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('cascade');
            $table->string('remarks')->nullable();

            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
