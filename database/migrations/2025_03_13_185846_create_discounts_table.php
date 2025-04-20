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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('kd_discount', 100)->unique();
            $table->string('name', 100);
            $table->enum('type', ['category', 'product',]);
            $table->decimal('discount_percentage', 5, 2);
            $table->boolean('is_member_only')->default(false);
            $table->text('member_tiers')->nullable(); 
            $table->integer('min_quantity')->nullable(); // Berlaku untuk bulk purchase
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
