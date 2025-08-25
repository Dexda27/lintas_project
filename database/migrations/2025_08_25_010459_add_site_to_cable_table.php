<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cables', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['source_site_id']);
            $table->dropForeign(['destination_site_id']);

            // Drop the old columns
            $table->dropColumn(['source_site_id', 'destination_site_id']);

            // Add new string columns
            $table->string('source_site')->after('region');
            $table->string('destination_site')->after('source_site');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cables', function (Blueprint $table) {
            // Drop the new string columns
            $table->dropColumn(['source_site', 'destination_site']);

            // Add back the foreign key columns
            $table->foreignId('source_site_id')->after('region')->constrained('sites');
            $table->foreignId('destination_site_id')->after('source_site_id')->constrained('sites');
        });
    }
};