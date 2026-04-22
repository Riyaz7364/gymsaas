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
        Schema::table('trainer_schedules', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('max_members')
                  ->comment('Per-session fee in local currency; null = free/included');
        });
    }

    public function down(): void
    {
        Schema::table('trainer_schedules', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
