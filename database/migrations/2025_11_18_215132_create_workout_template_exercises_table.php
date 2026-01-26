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
        Schema::create('workout_template_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_template_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained('workout_exercises')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->integer('target_sets')->default(3);
            $table->integer('target_reps')->default(10);
            $table->decimal('target_weight', 8, 2)->default(0);
            $table->integer('rest_seconds')->default(120);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_template_exercises');
    }
};
