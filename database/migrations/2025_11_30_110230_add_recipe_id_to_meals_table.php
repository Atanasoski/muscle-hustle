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
        Schema::table('meals', function (Blueprint $table) {
            // Link to recipe (nullable for backward compatibility and manual meals)
            $table->foreignId('recipe_id')->nullable()->after('meal_plan_id')->constrained()->onDelete('set null');

            // Serving multiplier (1.0 = default recipe servings, 1.5 = 1.5x servings, etc.)
            $table->decimal('servings', 8, 2)->default(1.0)->after('recipe_id');

            // Add notes field for meal-specific notes
            $table->text('notes')->nullable()->after('fat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropForeign(['recipe_id']);
            $table->dropColumn(['recipe_id', 'servings', 'notes']);
        });
    }
};
