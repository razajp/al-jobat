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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->string('order_no')->nullable();
            $table->string('shipment_no')->nullable();
            $table->date('date');
            $table->integer('netAmount');
            $table->unsignedBigInteger('customer_id');
            $table->json('articles_in_invoice');
            $table->timestamps();

            $table->foreign('order_no')->references('order_no')->on('orders')->onDelete('set null');
            $table->foreign('shipment_no')->references('shipment_no')->on('shipments')->onDelete('set null');

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};