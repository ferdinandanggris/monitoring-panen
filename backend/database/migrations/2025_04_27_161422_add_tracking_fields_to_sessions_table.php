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
            $table->renameColumn('area', 'total_area');
            $table->double('total_distance')->default(0)->after('total_area');
            $table->unsignedBigInteger('last_sequence_session_detail')->nullable()->after('total_distance');
            $table->timestamp('last_calculate_at')->nullable()->after('last_sequence_session_detail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session', function (Blueprint $table) {
            $table->renameColumn('total_area', 'area');
            $table->dropColumn(['total_distance', 'last_sequence_session_detail', 'last_calculate_at']);
        });
    }
};
