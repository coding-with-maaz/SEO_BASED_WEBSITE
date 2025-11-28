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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('movie'); // movie, tv_show, web_series, documentary, short_film, etc.
            $table->string('content_type')->default('custom'); // custom, tmdb
            $table->string('tmdb_id')->nullable(); // If linked to TMDB
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->date('release_date')->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('episode_count')->nullable(); // For TV shows
            $table->string('status')->default('published'); // published, draft, upcoming
            $table->json('genres')->nullable();
            $table->json('cast')->nullable();
            $table->string('language')->nullable();
            $table->string('dubbing_language')->nullable(); // Hindi, English, etc.
            $table->text('download_link')->nullable();
            $table->text('watch_link')->nullable();
            $table->integer('views')->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('type');
            $table->index('status');
            $table->index('is_featured');
            $table->index('release_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
