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
        Schema::table('workout_templates', function (Blueprint $table) {
            $table->unsignedInteger('week_number')->default(1)->after('day_of_week');
            $table->unsignedInteger('order_index')->default(0)->after('week_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_templates', function (Blueprint $table) {
            $table->dropColumn(['week_number', 'order_index']);
        });
    }
};
