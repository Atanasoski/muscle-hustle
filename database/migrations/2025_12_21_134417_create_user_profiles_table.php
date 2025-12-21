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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('fitness_goal')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->integer('height')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('training_experience')->nullable();
            $table->integer('training_days_per_week')->nullable();
            $table->integer('workout_duration_minutes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
