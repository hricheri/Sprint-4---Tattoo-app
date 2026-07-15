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
        Schema::create('studios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->unique()->constrained()->onDelete('cascade');
            $table->string('name')->nullable(false);
            $table->string('city')->nullable(false);
            $table->string('address')->nullable();
            $table->enum('cost_type', ['renta_fija', 'porcentaje', 'dueño_sin_costo']);
            $table->decimal('cost_amount', 10, 2)->nullable();
            $table->enum('studio_type', ['individual', 'compartido']);
            $table->string('access_instructions', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studios');
    }
};
