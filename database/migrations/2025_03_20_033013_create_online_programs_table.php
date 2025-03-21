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
        Schema::create('online_programs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('customer_id');
            $table->string('category');
            $table->morphs('sub_category');
            $table->integer('amount');
            $table->string('remarks');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_programs');
    }
};
