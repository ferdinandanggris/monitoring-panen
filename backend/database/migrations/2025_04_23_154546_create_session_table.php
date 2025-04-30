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
        Schema::create('session', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
            $table->float('area')->default(0);
            $table->float('average_speed')->default(0);
            $table->foreignId('machine_id')->constrained('machine')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('driver')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session');
    }
};
