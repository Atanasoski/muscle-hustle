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
        Schema::create('workout_splits', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('days_per_week');
            $table->string('focus');
            $table->tinyInteger('day_index');
            $table->json('target_regions');
            $table->timestamps();

            $table->unique(['days_per_week', 'focus', 'day_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_splits');
    }
};
