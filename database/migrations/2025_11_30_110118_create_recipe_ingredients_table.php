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
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->foreignId('food_id')->constrained('foods')->onDelete('cascade');

            // Amount of this ingredient
            $table->decimal('quantity', 10, 2);
            $table->string('unit'); // g, ml, cup, tbsp, piece, etc.

            // Optional notes for this ingredient
            $table->string('notes')->nullable(); // "chopped", "cooked", "raw", etc.

            // Order in recipe
            $table->integer('order')->default(0);

            $table->timestamps();

            $table->index(['recipe_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};
