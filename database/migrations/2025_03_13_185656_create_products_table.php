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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kd_product')->unique();
            $table->string('name');
            $table->decimal('harga');
            $table->integer('stok');
            $table->string('image')->nullable();
            $table->date('expired_at')->nullable(); // Tanggal kedaluwarsa (untuk makanan/minuman)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
