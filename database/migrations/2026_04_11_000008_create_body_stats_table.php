<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('member_id');
            $table->date('date');
            $table->decimal('weight', 6, 2)->nullable();   // kg
            $table->decimal('height', 5, 2)->nullable();   // cm
            $table->decimal('bmi', 5, 2)->nullable();
            $table->decimal('body_fat_pct', 5, 2)->nullable();
            $table->decimal('muscle_mass', 6, 2)->nullable();
            $table->decimal('chest', 5, 2)->nullable();    // cm
            $table->decimal('waist', 5, 2)->nullable();
            $table->decimal('hips', 5, 2)->nullable();
            $table->decimal('arms', 5, 2)->nullable();
            $table->decimal('thighs', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
            $table->index(['member_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_stats');
    }
};
