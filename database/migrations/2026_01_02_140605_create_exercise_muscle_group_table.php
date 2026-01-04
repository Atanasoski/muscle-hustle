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
        Schema::create('exercise_muscle_group', function (Blueprint $table) {
            $table->foreignId('exercise_id')->constrained('workout_exercises')->cascadeOnDelete();
            $table->foreignId('muscle_group_id')->constrained('muscle_groups')->cascadeOnDelete();
            $table->boolean('is_primary')->default(true);

            $table->primary(['exercise_id', 'muscle_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_muscle_group');
    }
};
