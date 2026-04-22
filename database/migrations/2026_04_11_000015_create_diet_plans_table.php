<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diet_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('member_id')->nullable(); // null = default template
            $table->unsignedBigInteger('created_by');           // user_id
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('goal', ['weight_loss', 'muscle_gain', 'maintain', 'endurance'])->default('maintain');
            $table->boolean('is_default')->default(false);
            $table->boolean('ai_generated')->default(false);
            $table->text('ai_prompt_used')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('member_id')->references('id')->on('members')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
        });

        Schema::create('diet_meals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->enum('meal_type', ['breakfast', 'morning_snack', 'lunch', 'evening_snack', 'dinner', 'post_workout']);
            $table->enum('day_of_week', ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'all'])->default('all');
            $table->json('foods');   // [{"name":"Oats","quantity":"1 cup","calories":150,"protein":5}]
            $table->unsignedInteger('total_calories')->nullable();
            $table->decimal('protein_g', 6, 2)->nullable();
            $table->decimal('carbs_g', 6, 2)->nullable();
            $table->decimal('fat_g', 6, 2)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('diet_plans')->cascadeOnDelete();
            $table->index(['plan_id', 'day_of_week', 'meal_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_meals');
        Schema::dropIfExists('diet_plans');
    }
};
