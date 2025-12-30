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
        Schema::create('workout_session_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained('workout_exercises')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->integer('target_sets')->nullable();
            $table->integer('target_reps')->nullable();
            $table->decimal('target_weight', 8, 2)->nullable();
            $table->integer('rest_seconds')->nullable();
            $table->timestamps();

            // Performance indexes
            $table->index(['workout_session_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_session_exercises');
    }
};
