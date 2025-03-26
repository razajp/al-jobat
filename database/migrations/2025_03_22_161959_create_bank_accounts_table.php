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
            $table->nullableMorphs('sub_category');
            $table->string('bank_id');
            $table->string('account_title');
            $table->date('date');
            $table->string('remarks')->nullable();
            $table->string('account_no')->nullable();
            $table->string('chqbk_serial_start')->nullable();
            $table->string('chqbk_serial_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
}
