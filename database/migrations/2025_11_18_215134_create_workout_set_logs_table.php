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
        Schema::create('workout_session_set_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained('workout_exercises')->onDelete('cascade');
            $table->integer('set_number');
            $table->decimal('weight', 8, 1);
            $table->integer('reps');
            $table->integer('rest_seconds')->nullable();
            $table->timestamps();

            // Performance indexes
            $table->index(['exercise_id', 'workout_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_session_set_logs');
    }
};
