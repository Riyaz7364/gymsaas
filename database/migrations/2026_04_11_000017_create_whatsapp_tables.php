<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('member_id')->nullable();
            $table->string('to_phone');
            $table->enum('direction', ['outbound', 'inbound'])->default('outbound');
            $table->string('template')->nullable();
            $table->string('trigger')->nullable();  // attendance, expiry_reminder, campaign, inbound etc.
            $table->longText('body');
            $table->enum('status', ['sent', 'delivered', 'read', 'failed', 'pending'])->default('pending');
            $table->string('whatsapp_message_id')->nullable();
            $table->json('meta_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('member_id')->references('id')->on('members')->nullOnDelete();
            $table->index(['gym_id', 'direction', 'status']);
            $table->index(['member_id', 'created_at']);
        });

        Schema::create('member_ai_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('attendance_id')->nullable();
            $table->text('ai_context')->nullable();       // JSON context sent to GPT
            $table->longText('ai_response');              // raw GPT response
            $table->longText('whatsapp_body');            // formatted message sent
            $table->boolean('sent_via_whatsapp')->default(false);
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
            $table->foreign('attendance_id')->references('id')->on('attendances')->nullOnDelete();
            $table->index(['member_id', 'created_at']);
        });

        Schema::create('whatsapp_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->string('name');
            $table->json('target_filter')->nullable();   // {status: "active", plan_id: 5}
            $table->string('template')->nullable();
            $table->longText('body');
            $table->timestamp('scheduled_at')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'cancelled'])->default('draft');
            $table->unsignedInteger('total_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_campaigns');
        Schema::dropIfExists('member_ai_messages');
        Schema::dropIfExists('whatsapp_logs');
    }
};
