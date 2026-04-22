<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_workout_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('member_id');
            $table->unsignedTinyInteger('current_step')->default(1);   // which step in the sequence
            $table->unsignedTinyInteger('total_steps')->default(5);    // size of the cycle
            $table->enum('plan_type', ['default', 'ai'])->default('default');
            $table->date('last_checkin_date')->nullable();              // prevents double-advance same day
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
            $table->unique(['member_id', 'gym_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_workout_progress');
    }
};
