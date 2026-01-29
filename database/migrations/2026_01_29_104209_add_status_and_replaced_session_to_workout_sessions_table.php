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
        Schema::table('workout_sessions', function (Blueprint $table) {
            // Add status enum column
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])
                ->default('draft')
                ->after('is_auto_generated');

            // Add replaced_session_id for regeneration tracking
            $table->foreignId('replaced_session_id')
                ->nullable()
                ->after('status')
                ->constrained('workout_sessions')
                ->nullOnDelete();

            // Make performed_at nullable (draft sessions won't have it set)
            $table->dateTime('performed_at')->nullable()->change();

            // Add index on status for efficient filtering
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_sessions', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex(['status']);

            // Drop foreign key and column
            $table->dropForeign(['replaced_session_id']);
            $table->dropColumn('replaced_session_id');

            // Drop status column
            $table->dropColumn('status');

            // Make performed_at not nullable again
            $table->dateTime('performed_at')->nullable(false)->change();
        });
    }
};
