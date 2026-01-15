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
        Schema::create('partner_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained('workout_exercises')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamps();

            $table->unique(['partner_id', 'exercise_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_exercises');
    }
};
