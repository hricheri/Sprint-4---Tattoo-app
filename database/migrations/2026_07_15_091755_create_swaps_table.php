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
        Schema::create('swaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_a_id')->constrained('artists')->onDelete('cascade');
            $table->foreignId('artist_b_id')->constrained('artists')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pendiente', 'aceptado', 'rechazado', 'completado', 'cancelado'])->default('pendiente');
            $table->boolean('includes_money_exchange')->default(false);
            $table->string('promo_image')->nullable();
            $table->string('promo_caption', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swaps');
    }
};
