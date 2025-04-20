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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('member_id')->nullable()->constrained()->onDelete('set null'); 
            $table->decimal('total_amount', 15, 2); 
            $table->decimal('discount_total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2); 
            $table->decimal('change_amount', 15, 2); 
            $table->integer('earned_points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
