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
        Schema::dropIfExists('seo_pages');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration drops the table permanently
        // If you need to restore it, you would need to recreate it manually
    }
};
