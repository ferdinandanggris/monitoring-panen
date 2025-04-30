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
        Schema::create('session_detail', function (Blueprint $table) {
            $table->id();
            $table->dateTime('recorded_at');
            $table->unsignedInteger('sequence');
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
            $table->float('speed')->nullable();
            $table->foreignId('session_id')->constrained('session')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_detail');
    }
};
