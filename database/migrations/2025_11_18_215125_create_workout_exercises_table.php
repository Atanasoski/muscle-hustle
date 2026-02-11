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
        Schema::create('workout_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('movement_pattern_id')->nullable()->constrained('movement_patterns')->restrictOnDelete();
            $table->foreignId('target_region_id')->nullable()->constrained('target_regions')->restrictOnDelete();
            $table->foreignId('equipment_type_id')->nullable()->constrained('equipment_types')->restrictOnDelete();
            $table->foreignId('angle_id')->nullable()->constrained('angles')->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('muscle_group_image')->nullable();
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->integer('default_rest_sec')->nullable();
            $table->timestamps();

            $table->index(['movement_pattern_id', 'equipment_type_id']);
            $table->index(['target_region_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_exercises');
    }
};
