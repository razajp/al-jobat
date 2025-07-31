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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->foreignId('work_id')->constrained('setups')->onDelete('cascade');
            $table->foreignId('worker_id')->constrained('employees')->onDelete('cascade');
            $table->json('tags');
            $table->string('title');
            $table->decimal('rate', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
