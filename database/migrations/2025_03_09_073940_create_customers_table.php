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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('customer_name')->unique();  // Fixed the typo here
            $table->string('person_name');  // Fixed the typo here
            $table->string('urdu_title')->nullable();
            $table->string('phone_number');
            $table->date('date');
            $table->string('category');
            $table->unsignedBigInteger('city_id');
            $table->string('address');
            $table->timestamps();

            $table->foreign('city_id')->references('id')->on('setups')->onDelete('cascade');
            
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};