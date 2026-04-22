<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('member_id');
            $table->timestamp('check_in');
            $table->timestamp('check_out')->nullable();
            $table->enum('method', ['manual', 'qr'])->default('manual');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
            $table->index(['gym_id', 'check_in']);
            $table->index(['member_id', 'check_in']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
