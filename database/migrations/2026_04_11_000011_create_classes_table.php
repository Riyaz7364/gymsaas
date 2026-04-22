<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('schedule_days')->nullable();  // ["mon","wed","fri"]
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('capacity')->default(20);
            $table->string('room')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('trainer_id')->references('id')->on('trainers')->nullOnDelete();
        });

        Schema::create('class_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('member_id');
            $table->date('booking_date');
            $table->enum('status', ['booked', 'attended', 'cancelled', 'no_show'])->default('booked');
            $table->timestamps();

            $table->unique(['class_id', 'member_id', 'booking_date']);
            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
            $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_bookings');
        Schema::dropIfExists('classes');
    }
};
