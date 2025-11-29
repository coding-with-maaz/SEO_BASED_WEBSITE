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
        // Add series_status column if it doesn't exist
        if (!Schema::hasColumn('contents', 'series_status')) {
            Schema::table('contents', function (Blueprint $table) {
                $table->string('series_status')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove series_status column if it exists
        if (Schema::hasColumn('contents', 'series_status')) {
            Schema::table('contents', function (Blueprint $table) {
                $table->dropColumn('series_status');
            });
        }
    }
};
