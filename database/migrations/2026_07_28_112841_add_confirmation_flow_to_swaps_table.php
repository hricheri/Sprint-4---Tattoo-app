<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('swaps', function (Blueprint $table) {
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
            $table->boolean('confirmed_by_a')->default(false)->after('status');
            $table->boolean('confirmed_by_b')->default(false)->after('confirmed_by_a');
        });
    }

    public function down(): void
    {
        Schema::table('swaps', function (Blueprint $table) {
            $table->dropColumn(['confirmed_by_a', 'confirmed_by_b']);
            $table->date('start_date')->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
        });
    }
};