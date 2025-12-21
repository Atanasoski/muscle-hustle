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
        Schema::table('partner_identities', function (Blueprint $table) {
            // Essential colors
            $table->string('background_color')->nullable()->after('font_family');
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_identities', function (Blueprint $table) {
            $table->dropColumn([
                'background_color',
                'card_background_color',
                'text_primary_color',
                'text_secondary_color',
                'text_on_primary_color',
                'success_color',
                'warning_color',
                'danger_color',
                'accent_color',
                'border_color',
                'background_pattern',
            ]);
        });
    }
};
