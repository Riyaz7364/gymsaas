<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('trainer_schedules')) {
            Schema::create('trainer_schedules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('gym_id');
                $table->unsignedBigInteger('trainer_id');
                $table->string('title');
                $table->enum('day_of_week', ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']);
                $table->time('start_time');
                $table->time('end_time');
                $table->unsignedInteger('max_members')->default(1);
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
                $table->foreign('trainer_id')->references('id')->on('trainers')->cascadeOnDelete();
                $table->index(['gym_id', 'trainer_id', 'is_active']);
            });
        }

        if (!Schema::hasTable('trainer_schedule_members')) {
            Schema::create('trainer_schedule_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('schedule_id');
                $table->unsignedBigInteger('member_id');
                $table->date('assigned_at')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->timestamps();

                $table->unique(['schedule_id', 'member_id']);
                $table->foreign('schedule_id')->references('id')->on('trainer_schedules')->cascadeOnDelete();
                $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_schedule_members');
        Schema::dropIfExists('trainer_schedules');
    }
};
