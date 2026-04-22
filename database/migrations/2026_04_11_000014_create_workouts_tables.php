<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->string('name');
            $table->string('icon')->nullable();
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
        });

        Schema::create('workout_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('muscle_group')->nullable();
            $table->string('equipment')->nullable();
            $table->string('video_url')->nullable();
            $table->string('image')->nullable();
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('workout_categories')->nullOnDelete();
        });

        Schema::create('workout_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('member_id')->nullable();    // null = default plan
            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('member_id')->references('id')->on('members')->nullOnDelete();
            $table->foreign('trainer_id')->references('id')->on('trainers')->nullOnDelete();
        });

        Schema::create('workout_plan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('activity_id');
            $table->enum('day_of_week', ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']);
            $table->unsignedInteger('sets')->nullable();
            $table->string('reps')->nullable();         // "12-15" or "AMRAP"
            $table->unsignedInteger('duration_secs')->nullable();
            $table->unsignedInteger('rest_secs')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('workout_plans')->cascadeOnDelete();
            $table->foreign('activity_id')->references('id')->on('workout_activities')->cascadeOnDelete();
            $table->index(['plan_id', 'day_of_week']);
        });

        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('plan_item_id')->nullable();
            $table->unsignedBigInteger('activity_id');
            $table->date('date');
            $table->unsignedInteger('sets_done')->nullable();
            $table->string('reps_done')->nullable();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->unsignedInteger('duration_secs')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
            $table->foreign('activity_id')->references('id')->on('workout_activities')->cascadeOnDelete();
            $table->index(['member_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_logs');
        Schema::dropIfExists('workout_plan_items');
        Schema::dropIfExists('workout_plans');
        Schema::dropIfExists('workout_activities');
        Schema::dropIfExists('workout_categories');
    }
};
