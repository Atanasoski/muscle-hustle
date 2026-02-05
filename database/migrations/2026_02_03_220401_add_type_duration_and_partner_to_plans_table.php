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
        Schema::table('plans', function (Blueprint $table) {
            $table->string('type')->default('custom')->after('user_id');
            $table->unsignedInteger('duration_weeks')->nullable()->after('description');
            $table->foreignId('partner_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->dropColumn(['type', 'duration_weeks', 'partner_id']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
