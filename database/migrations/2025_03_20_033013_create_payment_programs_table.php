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
            $table->unsignedBigInteger('prg_no')->unique();
            $table->date('date');
            $table->unsignedBigInteger('customer_id');
            $table->string('category');
            $table->nullableMorphs('sub_category');
            $table->integer('amount');
            $table->string('remarks')->nullable();;
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_programs');
    }
}
