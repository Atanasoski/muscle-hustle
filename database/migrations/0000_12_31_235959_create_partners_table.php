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
        // Create partners table
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // Create partner_identities table
        Schema::create('partner_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->string('primary_color')->default('#ff6b35');
            $table->string('secondary_color')->default('#4ecdc4');
            $table->string('logo')->nullable();
            $table->string('font_family')->nullable();

            // Essential colors
            $table->string('background_color')->nullable();
            $table->string('card_background_color')->nullable();
            $table->string('text_primary_color')->nullable();
            $table->string('text_secondary_color')->nullable();
            $table->string('text_on_primary_color')->nullable();

            // Semantic colors
            $table->string('success_color')->nullable();
            $table->string('warning_color')->nullable();
            $table->string('danger_color')->nullable();

            // Optional styling
            $table->string('accent_color')->nullable();
            $table->string('border_color')->nullable();
            $table->string('background_pattern')->nullable();

            // Dark mode colors
            $table->string('primary_color_dark')->nullable();
            $table->string('secondary_color_dark')->nullable();
            $table->string('background_color_dark')->nullable();
            $table->string('card_background_color_dark')->nullable();
            $table->string('text_primary_color_dark')->nullable();
            $table->string('text_secondary_color_dark')->nullable();
            $table->string('text_on_primary_color_dark')->nullable();
            $table->string('success_color_dark')->nullable();
            $table->string('warning_color_dark')->nullable();
            $table->string('danger_color_dark')->nullable();
            $table->string('accent_color_dark')->nullable();
            $table->string('border_color_dark')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_identities');
        Schema::dropIfExists('partners');
    }
};
