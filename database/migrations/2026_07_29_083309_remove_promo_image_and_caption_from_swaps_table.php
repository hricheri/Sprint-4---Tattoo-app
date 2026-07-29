<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('swaps', function (Blueprint $table) {
            $table->dropColumn(['promo_image', 'promo_caption']);
        });
    }

    public function down(): void
    {
        Schema::table('swaps', function (Blueprint $table) {
            $table->string('promo_image')->nullable();
            $table->string('promo_caption')->nullable();
        });
    }
};
