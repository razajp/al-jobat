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
        Schema::create('payment_clears', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('customer_payments')->onDelete('cascade');
            $table->date('clear_date');
            $table->string('method');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->onDelete('cascade');
            $table->integer('amount');
            $table->string('reff_no')->unique();
            $table->string('remarks')->nullable();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_clears');
    }
};
