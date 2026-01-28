<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
/**
 * Run the migrations.
 *
 * Adds classification foreign key columns as NULLABLE.
 * The ExerciseClassificationSeeder will populate these columns.
 *
 * To enforce NOT NULL after seeding, see:
 * database/migrations/_manual_make_classification_columns_required.php
 */
    public function up(): void
    {
        Schema::table('workout_exercises', function (Blueprint $table) {
            // All columns start as nullable for safe backfill of existing data
            $table->foreignId('movement_pattern_id')
                ->nullable()
                ->after('category_id')
                ->constrained('movement_patterns')
                ->restrictOnDelete();

            $table->foreignId('target_region_id')
                ->nullable()
                ->after('movement_pattern_id')
                ->constrained('target_regions')
                ->restrictOnDelete();

            $table->foreignId('equipment_type_id')
                ->nullable()
                ->after('target_region_id')
                ->constrained('equipment_types')
                ->restrictOnDelete();

            $table->foreignId('angle_id')
                ->nullable()
                ->after('equipment_type_id')
                ->constrained('angles')
                ->restrictOnDelete();

            // Add indexes for common queries
            $table->index(['movement_pattern_id', 'equipment_type_id']);
            $table->index(['target_region_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_exercises', function (Blueprint $table) {
            $table->dropIndex(['movement_pattern_id', 'equipment_type_id']);
            $table->dropIndex(['target_region_id']);

            $table->dropForeign(['movement_pattern_id']);
            $table->dropForeign(['target_region_id']);
            $table->dropForeign(['equipment_type_id']);
            $table->dropForeign(['angle_id']);

            $table->dropColumn([
                'movement_pattern_id',
                'target_region_id',
                'equipment_type_id',
                'angle_id',
            ]);
        });
    }
};
