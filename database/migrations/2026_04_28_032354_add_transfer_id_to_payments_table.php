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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'transfer_id')) {
                $table->string('transfer_id')->nullable()->after('razorpay_payment_id');
            }
            if (!Schema::hasColumn('payments', 'transfer_status')) {
                $table->enum('transfer_status', ['pending', 'completed', 'failed'])->default('pending')->after('transfer_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'transfer_id')) {
                $table->dropColumn('transfer_id');
            }
            if (Schema::hasColumn('payments', 'transfer_status')) {
                $table->dropColumn('transfer_status');
            }
        });
    }
};
