<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content;
use App\Models\Episode;
use App\Models\EpisodeServer;
use Carbon\Carbon;

class EpisodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the first TV show content, or create a test one if none exists
        $content = Content::whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])
            ->first();

        if (!$content) {
            // Create a test TV show if none exists (using firstOrCreate to avoid duplicates)
            $content = Content::firstOrCreate(
                [
                    'title' => 'Test TV Show',
                    'type' => 'tv_show',
                ],
                [
                    'description' => 'A test TV show for episodes',
                    'content_type' => 'custom',
                    'status' => 'published',
                    'release_date' => Carbon::now()->subYear(),
                ]
            );
            
            $this->command->info("Created/Found test TV show: {$content->title} (ID: {$content->id})");
        } else {
            $this->command->info("Using existing TV show: {$content->title} (ID: {$content->id})");
        }

        // Check if Episode 1 already exists
        $existingEpisode = Episode::where('content_id', $content->id)
            ->where('episode_number', 1)
            ->first();

        if ($existingEpisode) {
            $this->command->info("Episode 1 already exists for this TV show. Updating...");
            $episode = $existingEpisode;
            
            // Update episode details
            $episode->update([
                'title' => 'Episode 1',
                'is_published' => true,
            ]);
        } else {
            // Create Episode 1
            $episode = Episode::create([
                'content_id' => $content->id,
                'episode_number' => 1,
                'title' => 'Episode 1',
                'description' => 'The first episode of the series',
                'air_date' => Carbon::now()->subWeek(),
                'is_published' => true,
                'sort_order' => 1,
            ]);
            
            $this->command->info("Created Episode 1 (ID: {$episode->id})");
        }

        // Check if server already exists for this episode
        $existingServer = EpisodeServer::where('episode_id', $episode->id)
            ->where('watch_link', 'https://turbovidhls.com/t/69274f0b9d2f3')
            ->first();

        if ($existingServer) {
            $this->command->info("Server with this watch link already exists. Updating...");
            $existingServer->update([
                'server_name' => 'TurbovidHLS',
                'quality' => 'HD',
                'watch_link' => 'https://turbovidhls.com/t/69274f0b9d2f3',
                'is_active' => true,
                'sort_order' => 1,
            ]);
            $this->command->info("Server updated successfully!");
        } else {
            // Create the server with TurbovidHLS watch link
            EpisodeServer::create([
                'episode_id' => $episode->id,
                'server_name' => 'TurbovidHLS',
                'quality' => 'HD',
                'watch_link' => 'https://turbovidhls.com/t/69274f0b9d2f3',
                'is_active' => true,
                'sort_order' => 1,
            ]);
            
            $this->command->info("Created server with TurbovidHLS watch link!");
        }

        // Add a second server for Episode 1
        $existingServer2 = EpisodeServer::where('episode_id', $episode->id)
            ->where('watch_link', 'https://turbovidhls.com/t/692786954e5b5')
            ->first();

        if ($existingServer2) {
            $this->command->info("Second server with this watch link already exists. Updating...");
            $existingServer2->update([
                'server_name' => 'TurbovidHLS 2',
                'quality' => 'HD',
                'watch_link' => 'https://turbovidhls.com/t/692786954e5b5',
                'is_active' => true,
                'sort_order' => 2,
            ]);
            $this->command->info("Second server updated successfully!");
        } else {
            // Create the second server
            EpisodeServer::create([
                'episode_id' => $episode->id,
                'server_name' => 'TurbovidHLS 2',
                'quality' => 'HD',
                'watch_link' => 'https://turbovidhls.com/t/692786954e5b5',
                'is_active' => true,
                'sort_order' => 2,
            ]);
            
            $this->command->info("Created second server with TurbovidHLS watch link!");
        }

        $this->command->info("✅ Episode 1 seeded successfully!");
        $this->command->info("TV Show URL: /tv-shows/custom_{$content->id}");
        $this->command->info("Manage Episodes: /admin/contents/{$content->id}/episodes");
    }
}

