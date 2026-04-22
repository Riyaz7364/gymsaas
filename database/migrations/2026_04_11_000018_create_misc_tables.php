<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->string('name');
            $table->string('color', 7)->default('#0d6efd');
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('event_type_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('start_datetime');
            $table->datetime('end_datetime')->nullable();
            $table->boolean('all_day')->default(false);
            $table->string('color', 7)->nullable();
            $table->string('location')->nullable();
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('event_type_id')->references('id')->on('event_types')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->index(['gym_id', 'start_datetime']);
        });

        Schema::create('lockers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->string('locker_no');
            $table->unsignedBigInteger('member_id')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->text('notes')->nullable();
            $table->date('assigned_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['gym_id', 'locker_no']);
            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('member_id')->references('id')->on('members')->nullOnDelete();
        });

        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('created_by');
            $table->string('title');
            $table->longText('body');
            $table->enum('audience', ['all', 'trainers', 'members', 'staff'])->default('all');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
        });

        Schema::create('contact_diary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->string('contact_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('type')->nullable();  // prospect, vendor, partner etc.
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->enum('status', ['open', 'done', 'cancelled'])->default('open');
            $table->timestamps();

            $table->foreign('gym_id')->references('id')->on('gyms')->cascadeOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_diary');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('lockers');
        Schema::dropIfExists('events');
        Schema::dropIfExists('event_types');
    }
};
