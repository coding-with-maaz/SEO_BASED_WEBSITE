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
        // Add end_date column if it doesn't exist
        if (!Schema::hasColumn('contents', 'end_date')) {
            Schema::table('contents', function (Blueprint $table) {
                $table->date('end_date')->nullable()->after('series_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove end_date column if it exists
        if (Schema::hasColumn('contents', 'end_date')) {
            Schema::table('contents', function (Blueprint $table) {
                $table->dropColumn('end_date');
            });
        }
    }
};
