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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->date('date');
            $table->string('type');
            $table->integer('amount');
            $table->string('cheque_no')->nullable();
            $table->string('slip_no')->nullable();
            $table->string('transition_id')->nullable();
            $table->date('cheque_date')->nullable();
            $table->date('slip_date')->nullable();
            $table->date('clear_date')->nullable();
            $table->string('bank')->nullable();
            $table->string('remarks')->nullable();
            $table->string('program_no')->nullable();

            // Define foreign key constraint
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('program_no')->references('program_no')->on('payment_programs')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
