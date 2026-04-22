<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diet_meals', function (Blueprint $table) {
            if (!Schema::hasColumn('diet_meals', 'name')) {
                $table->string('name')->after('plan_id')->default('');
            }
            if (!Schema::hasColumn('diet_meals', 'time')) {
                $table->string('time', 20)->nullable()->after('name'); // e.g. "08:00 AM"
            }
        });
    }

    public function down(): void
    {
        Schema::table('diet_meals', function (Blueprint $table) {
            $table->dropColumn(['name', 'time']);
        });
    }
};
