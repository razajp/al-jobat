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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('status')->default('active');
            $table->string('profile_picture')->default('default_avatar.png');
            $table->string('theme')->default('light');
            $table->string('layout')->nullable();
            $table->string('invoice_type')->default('order');
            $table->string('voucher_type')->default('supplier');
            $table->string('production_type')->default('issue');
            $table->string('daily_ledger_type')->default('deposit');

            $table->json('menu_shortcuts')->default('[]');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
