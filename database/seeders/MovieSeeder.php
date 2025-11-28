<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content;
use Carbon\Carbon;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create a test movie
        $movie = Content::firstOrCreate(
            [
                'title' => 'Test Movie',
                'type' => 'movie',
            ],
            [
                'description' => 'A test movie for demonstration purposes. This is a sample movie content that can be used for testing the website functionality.',
                'content_type' => 'custom',
                'status' => 'published',
                'release_date' => Carbon::now()->subYear(),
                'rating' => 8.5,
                'genres' => ['Action', 'Drama', 'Thriller'],
                'cast' => [
                    ['name' => 'Actor One', 'character' => 'Main Character'],
                    ['name' => 'Actor Two', 'character' => 'Supporting Character'],
                    ['name' => 'Actor Three', 'character' => 'Villain'],
                ],
                'language' => 'English',
                'dubbing_language' => 'hindi',
                'poster_path' => 'https://via.placeholder.com/500x750?text=Test+Movie+Poster',
                'backdrop_path' => 'https://via.placeholder.com/1280x720?text=Test+Movie+Backdrop',
                'watch_link' => 'https://kdfeevideo.rpmvid.com/#heuz8d',
                'sort_order' => 0,
                'is_featured' => false,
            ]
        );

        // Update watch link if movie already exists
        if ($movie->wasRecentlyCreated === false) {
            $movie->watch_link = 'https://kdfeevideo.rpmvid.com/#heuz8d';
            $movie->save();
            $this->command->info("Updated watch link for existing movie.");
        }

        // Ensure slug is generated if it doesn't exist
        if (empty($movie->slug)) {
            $movie->slug = $movie->generateUniqueSlug();
            $movie->save();
        }

        $this->command->info("Created/Found test movie: {$movie->title} (ID: {$movie->id})");
        $this->command->info("Movie slug: {$movie->slug}");
        $this->command->info("Watch link: {$movie->watch_link}");
        $this->command->info("✅ Movie seeded successfully!");
        $this->command->info("Movie URL: /movies/{$movie->slug}");
        $this->command->info("Manage Movie: /admin/contents/{$movie->id}");
    }
}

