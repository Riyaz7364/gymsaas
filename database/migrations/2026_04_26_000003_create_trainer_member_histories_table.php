<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_member_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('trainer_id');
            $table->unsignedBigInteger('member_id');
            $table->enum('action', ['assigned', 'renewed', 'removed', 'left']);
            $table->unsignedBigInteger('related_plan_id')->nullable();
            $table->text('notes')->nullable();
            $table->date('occurred_at')->nullable();
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('trainer_id')->references('id')->on('trainers')->cascadeOnDelete();
            $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
            $table->foreign('related_plan_id')->references('id')->on('member_plans')->nullOnDelete();
            $table->index(
                ['gym_id', 'trainer_id', 'member_id', 'action'],
                'tmh_gym_trainer_member_action_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_member_histories');
    }
};
