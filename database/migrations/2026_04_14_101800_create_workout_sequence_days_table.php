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
        Schema::create('workout_sequence_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sequence_id')->constrained('workout_sequences')->cascadeOnDelete();
            $table->integer('day_number'); // 1, 2, 3, 4, 5, 6
            $table->string('label'); // e.g., "Chest Day", "Back Day"
            $table->string('icon'); // e.g., "💪", "🏋️"
            $table->string('color'); // e.g., "#3b82f6"
            $table->string('bg'); // e.g., "#eff6ff"
            $table->string('border'); // e.g., "#bfdbfe"
            $table->json('muscle_groups')->nullable(); // ['Chest', 'Triceps']
            $table->timestamps();
            
            $table->unique(['sequence_id', 'day_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_sequence_days');
    }
};
