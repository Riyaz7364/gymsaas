<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->index('gym_id');
        });

        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->string('serving_size')->nullable();
            $table->string('serving_unit')->default('g');
            $table->unsignedInteger('calories')->nullable();
            $table->decimal('protein_g', 6, 2)->nullable();
            $table->decimal('carbs_g', 6, 2)->nullable();
            $table->decimal('fat_g', 6, 2)->nullable();
            $table->decimal('fiber_g', 6, 2)->nullable();
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('food_categories')->cascadeOnDelete();
            $table->index('gym_id');
            $table->index('category_id');
            $table->unique(['gym_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_items');
        Schema::dropIfExists('food_categories');
    }
};
