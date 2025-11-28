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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents')->onDelete('cascade');
            $table->integer('episode_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->date('air_date')->nullable();
            $table->integer('duration')->nullable(); // Duration in minutes
            $table->integer('views')->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('content_id');
            $table->index('episode_number');
            $table->unique(['content_id', 'episode_number']);
        });
        
        Schema::create('episode_servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('episode_id')->constrained('episodes')->onDelete('cascade');
            $table->string('server_name'); // Streamtape, Clicknupload, Sendnow, etc.
            $table->string('quality')->nullable(); // HD, SD, 4K, etc.
            $table->text('download_link')->nullable();
            $table->text('watch_link')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('episode_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episode_servers');
        Schema::dropIfExists('episodes');
    }
};
