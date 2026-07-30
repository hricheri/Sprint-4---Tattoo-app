<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('swaps', function (Blueprint $table) {
            $table->boolean('cancellation_resolved')->default(false)->after('cancelled_by_artist_id');
        });
    }

    public function down(): void
    {
        Schema::table('swaps', function (Blueprint $table) {
            $table->dropColumn('cancellation_resolved');
        });
    }
};
