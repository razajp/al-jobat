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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->nullableMorphs('sub_category'); // creates sub_category_type and sub_category_id
            $table->string('bank_id');
            $table->string('account_title');
            $table->date('date');
            $table->string('remarks')->nullable();
            $table->string('account_no')->unique()->nullable();
            $table->string('chqbk_serial_start')->nullable();
            $table->string('chqbk_serial_end')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

            // Composite unique constraint
            $table->unique(['account_title', 'bank_id', 'sub_category_type', 'sub_category_id'], 'unique_bank_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};