<?php

declare(strict_types=1);

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
        // Movement Patterns (e.g., PRESS, ROW, SQUAT, HINGE)
        Schema::create('movement_patterns', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('display_order')->index();
            $table->timestamps();
        });

        // Target Regions (e.g., UPPER_PUSH, UPPER_PULL, LOWER, ARMS, CORE)
        Schema::create('target_regions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('display_order')->index();
            $table->timestamps();
        });

        // Equipment Types (e.g., BARBELL, DUMBBELL, CABLE, MACHINE)
        Schema::create('equipment_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('display_order')->index();
            $table->timestamps();
        });

        // Angles (e.g., FLAT, INCLINE, DECLINE, VERTICAL)
        Schema::create('angles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('display_order')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angles');
        Schema::dropIfExists('equipment_types');
        Schema::dropIfExists('target_regions');
        Schema::dropIfExists('movement_patterns');
    }
};
