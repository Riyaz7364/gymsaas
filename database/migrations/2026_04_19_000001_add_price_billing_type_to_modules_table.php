<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->default(null)->after('is_active');
            $table->string('billing_type', 20)->nullable()->default(null)->after('price'); // monthly | one_time
            $table->string('icon', 50)->nullable()->default(null)->after('billing_type');
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['price', 'billing_type', 'icon']);
        });
    }
};
