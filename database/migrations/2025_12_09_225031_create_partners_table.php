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
            $table->boolean('is_active')->default(true);
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
            $table->timestamps();
        });

        // Add partner_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('partner_id')->nullable()->after('id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->dropColumn('partner_id');
        });

        Schema::dropIfExists('partner_identities');
        Schema::dropIfExists('partners');
    }
};
