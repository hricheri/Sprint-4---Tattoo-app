<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        Schema::table('homes', function (Blueprint $table) {
            $table->text('description')->nullable()->after('roommates_count');
            $table->dropColumn(['transport_type', 'transport_cost']);
        });
    }

    public function down(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('homes', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->enum('transport_type', ['caminando', 'transporte_publico', 'auto'])->nullable();
            $table->decimal('transport_cost', 10, 2)->nullable();
        });
    }
};