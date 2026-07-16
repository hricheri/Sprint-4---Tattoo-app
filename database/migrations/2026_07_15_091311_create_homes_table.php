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
        Schema::create('homes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->unique()->constrained()->onDelete('cascade');
            $table->unsignedInteger('roommates_count')->default(0);
            $table->unsignedInteger('distance_to_studio_minutes')->nullable();
            $table->enum('transport_type', ['caminando', 'transporte_publico', 'auto']);
            $table->decimal('transport_cost', 10, 2)->nullable();
            $table->string('access_instructions', 500)->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homes');
    }
};
