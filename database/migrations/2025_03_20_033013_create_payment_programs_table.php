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
        Schema::create('payment_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_no')->unique();
            $table->string('order_no')->nullable(); // Make sure it's nullable
            $table->date('date');
            $table->unsignedBigInteger('customer_id');
            $table->string('category');
            $table->nullableMorphs('sub_category');
            $table->integer('amount');
            $table->string('remarks')->nullable();;
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('order_no')->references('order_no')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_programs');
    }
};