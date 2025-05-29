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
            $table->unsignedBigInteger('supplier_id');
            $table->date('date');
            $table->string('type');
            $table->string('method');
            $table->integer('amount');
            $table->string('cheque_id')->nullable()->unique();
            $table->string('slip_id')->nullable()->unique();
            $table->string('remarks')->nullable();
            $table->string('program_id')->nullable();

            // Define foreign key constraint
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('payment_programs')->onDelete('set null');
            $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');

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
