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
        Schema::create('fabrics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('fabric_id')->constrained('setups')->onDelete('cascade');
            $table->string('color');
            $table->string('unit');
            $table->decimal('quantity', 8, 2);
            $table->string('reff_no')->nullable();
            $table->string('remarks')->nullable();
            $table->string('tag');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabrics');
    }
};
