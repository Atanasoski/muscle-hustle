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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();

            // Time estimates
            $table->integer('prep_time_minutes')->nullable(); // Prep time
            $table->integer('cook_time_minutes')->nullable(); // Cook time

            // Servings
            $table->decimal('servings', 8, 2)->default(1); // Default servings this recipe makes

            // Quick filters
            $table->string('meal_type')->nullable(); // breakfast, lunch, dinner, snack
            $table->boolean('is_favorite')->default(false);

            // Tags for filtering/searching
            $table->json('tags')->nullable(); // ['high-protein', 'quick', 'meal-prep', etc.]

            $table->timestamps();

            $table->index(['user_id', 'name']);
            $table->index('meal_type');
            $table->index('is_favorite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
