<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('swaps', function (Blueprint $table) {
            $table->boolean('confirmed_seen_by_a')->default(false)->after('confirmed_by_b');
            $table->boolean('confirmed_seen_by_b')->default(false)->after('confirmed_seen_by_a');
        });
    }

    public function down(): void
    {
        Schema::table('swaps', function (Blueprint $table) {
            $table->dropColumn(['confirmed_seen_by_a', 'confirmed_seen_by_b']);
        });
    }
};
