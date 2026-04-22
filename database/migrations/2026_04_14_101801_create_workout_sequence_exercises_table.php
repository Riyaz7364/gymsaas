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
        Schema::create('workout_sequence_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('day_id')->constrained('workout_sequence_days')->cascadeOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained('workout_activities')->nullOnDelete();
            $table->string('name')->nullable(); // Fallback if activity doesn't exist
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_sequence_exercises');
    }
};
