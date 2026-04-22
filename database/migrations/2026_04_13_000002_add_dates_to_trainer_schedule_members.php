<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainer_schedule_members', function (Blueprint $table) {
            if (!Schema::hasColumn('trainer_schedule_members', 'start_date')) {
                $table->date('start_date')->nullable()->after('assigned_at');
            }
            if (!Schema::hasColumn('trainer_schedule_members', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainer_schedule_members', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
