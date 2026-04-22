<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gyms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('subscription_plan')->default('basic'); // basic, pro, enterprise
            $table->enum('status', ['active', 'inactive', 'suspended', 'trial'])->default('trial');
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable()->default('India');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('currency')->default('INR');
            $table->string('timezone')->default('Asia/Kolkata');
            $table->string('date_format')->default('d/m/Y');
            $table->string('language')->default('en');
            $table->string('whatsapp_dispatch_time')->default('07:00'); // HH:MM
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gyms');
    }
};
