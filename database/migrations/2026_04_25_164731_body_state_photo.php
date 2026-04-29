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
        Schema::create('body_stat_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('body_stat_id');
            $table->string('photo_path');
            $table->timestamps();

            $table->foreign('body_stat_id')->references('id')->on('body_stats')->cascadeOnDelete();
            $table->index('body_stat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
