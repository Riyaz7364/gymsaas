<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->string('member_no')->nullable(); // e.g. GH-001
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('dob')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('status', ['active', 'inactive', 'frozen', 'expired'])->default('active');
            $table->enum('goal', ['weight_loss', 'muscle_gain', 'maintain', 'endurance'])->default('maintain');
            $table->boolean('whatsapp_optin')->default(true);
            $table->string('blood_group')->nullable();
            $table->string('occupation')->nullable();
            $table->date('joined_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->index(['gym_id', 'status']);
            $table->index(['gym_id', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
