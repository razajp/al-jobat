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
        Schema::create('returned_fabrics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('tag');
            $table->foreignId('worker_id')->constrained('employees')->onDelete('cascade');
            $table->decimal('quantity', 8, 2);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returned_fabrics');
    }
};
