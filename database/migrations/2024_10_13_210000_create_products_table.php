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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->string('product_name');
            $table->integer('rbp');
            $table->integer('eup');
            $table->integer('yield');
            $table->string('flag_zona')->nullable();
            $table->integer('kuota_nasional')->nullable();
            $table->integer('kuota_lokal')->nullable();
            $table->integer('total_kuota');
            $table->integer('validity');
            $table->string('flag_type')->nullable();
            $table->enum('product_type', ['SIM', 'VOUCHER']);
            $table->enum('denom', ['5-10 K', '10-15 K', '<30 K', '20 K', '25 K', '30 K', '40 K', '50 K', '60-70 K', '80-90 K', '100 K', '120 K', '150 K']);
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
