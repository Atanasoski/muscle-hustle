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
        Schema::create('workout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workout_template_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('performed_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_auto_generated')->default(false);
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->foreignId('replaced_session_id')->nullable()->constrained('workout_sessions')->nullOnDelete();
            $table->timestamps();

            // Performance indexes
            $table->index(['user_id', 'performed_at']);
            $table->index(['user_id', 'completed_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_sessions');
    }
};
