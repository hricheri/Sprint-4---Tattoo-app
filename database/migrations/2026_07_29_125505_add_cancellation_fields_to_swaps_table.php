<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('swaps', function (Blueprint $table) {
            $table->text('cancellation_message')->nullable()->after('status');
            $table->foreignId('cancelled_by_artist_id')->nullable()->after('cancellation_message')->constrained('artists')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('swaps', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by_artist_id']);
            $table->dropColumn(['cancellation_message', 'cancelled_by_artist_id']);
        });
    }
};