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
            $table->string('method');
            $table->integer('amount');
            $table->unsignedBigInteger('cheque_id')->nullable()->unique();
            $table->unsignedBigInteger('slip_id')->nullable()->unique();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();

            // Define foreign key constraint
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('payment_programs')->onDelete('set null');
            $table->foreign('slip_id')->references('id')->on('customer_payments')->onDelete('set null');
            $table->foreign('cheque_id')->references('id')->on('customer_payments')->onDelete('set null');
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('cascade');

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
