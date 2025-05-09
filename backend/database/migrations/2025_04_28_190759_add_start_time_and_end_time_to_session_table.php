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
        Schema::table('session', function (Blueprint $table) {
            $table->timestamp('start_time')->nullable();
            $table->timestamp('last_update_at')->nullable();
            $table->timestamp('end_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'last_update_at', 'end_time']);
        });
    }
};
