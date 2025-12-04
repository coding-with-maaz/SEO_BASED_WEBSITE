<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to change column type from VARCHAR to TEXT
        // This works without requiring doctrine/dbal package
        DB::statement('ALTER TABLE `episodes` MODIFY `thumbnail_path` TEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to VARCHAR(255)
        DB::statement('ALTER TABLE `episodes` MODIFY `thumbnail_path` VARCHAR(255) NULL');
    }
};
