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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->integer('article_no')->unique();
            $table->date('date');
            $table->string('category');
            $table->string('size');
            $table->string('season');
            $table->integer('quantity')->unsigned();
            $table->integer('extra_pcs')->unsigned()->nullable();
            $table->string('fabric_type')->nullable();
            $table->decimal('sales_rate', 11, 2);
            $table->json('rates_array');
            $table->integer('ordered_quantity')->unsigned()->default(0);
            $table->integer('sold_quantity')->unsigned()->default(0);
            $table->integer('pcs_per_packet')->unsigned()->default(0);
            $table->string('image')->default('no_image_icon.png');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
