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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('kd_member', 100)->unique();
            $table->string('name', 100)->default('text');
            $table->string('no_telp', 100)->unique();
            $table->string('alamat', 100)->nullable()->default('text');
            $table->integer('points')->default(0);
            $table->enum('tier', ['bronze', 'silver', 'gold'])->default('bronze'); // Membership tier
            $table->date('last_transaction_date')->nullable(); // Date of the last transaction
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
