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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->foreignId('type_id')->constrained('setups')->onDelete('cascade');
            $table->string('employee_name');
            $table->string('urdu_title')->nullable();
            $table->string('phone_number');
            $table->date('joining_date');
            $table->string('cnic_no')->nullable();
            $table->integer('salary')->nullable();
            $table->string('status')->default('active');
            $table->string('profile_picture')->default('default_avatar.png');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
