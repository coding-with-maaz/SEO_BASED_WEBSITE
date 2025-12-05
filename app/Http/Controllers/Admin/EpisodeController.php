<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Episode;
use App\Models\EpisodeServer;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EpisodeController extends Controller
{
    protected $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }
    /**
     * Display a listing of episodes for a content.
     */
    public function index(Content $content)
    {
        $episodes = $content->episodes()->with('servers')->orderBy('episode_number')->get();
        
        // Get TV show details from TMDB to show available seasons
        $seasons = [];
        if ($content->tmdb_id) {
            $tvShowDetails = $this->tmdb->getTvShowDetails($content->tmdb_id);
            if ($tvShowDetails && isset($tvShowDetails['seasons'])) {
                // Filter out special seasons (season 0 is usually specials)
                $seasons = array_filter($tvShowDetails['seasons'], function($season) {
                    return isset($season['season_number']) && $season['season_number'] >= 1;
                });
                // Sort by season number
                usort($seasons, function($a, $b) {
                    return ($a['season_number'] ?? 0) <=> ($b['season_number'] ?? 0);
                });
                
                // Add existing episode counts for each season
                // Note: Episode numbers in database are sequential (1, 2, 3...), not per-season
                // For now, we'll show a simple count. Users can see which episodes already exist.
                $totalExisting = $episodes->count();
                foreach ($seasons as &$season) {
                    // For display purposes, we'll show total existing episodes
                    // The import function will skip duplicates based on episode_number
                    $season['existing_episodes'] = 0; // Will be calculated per episode during import
                }
            }
        }
        
        return view('admin.episodes.index', compact('content', 'episodes', 'seasons'));
    }

    /**
     * Show the form for creating a new episode.
     */
    public function create(Content $content)
    {
        return view('admin.episodes.create', compact('content'));
    }

    /**
     * Store a newly created episode.
     */
    public function store(Request $request, Content $content)
    {
        $validated = $request->validate([
            'episode_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail_path' => 'nullable|string',
            'air_date' => 'nullable|date',
            'duration' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['content_id'] = $content->id;
        $validated['is_published'] = $request->has('is_published') ? true : false;
        
        // Check if episode number already exists
        $existingEpisode = Episode::where('content_id', $content->id)
            ->where('episode_number', $validated['episode_number'])
            ->first();
        
        if ($existingEpisode) {
            return back()->withInput()->withErrors(['episode_number' => 'Episode number already exists for this content.']);
        }

        $episode = Episode::create($validated);

        // Automatically add embed servers if content has tmdb_id
        // This happens when episodes are created manually - embeds will be added if content was imported with embed option
        // For now, we'll just add vidsrc as default when tmdb_id exists
        if ($content->tmdb_id && in_array($content->type, ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])) {
            $this->addVidsrcEmbedToEpisode($episode, $content->tmdb_id);
        }

        return redirect()->route('admin.episodes.index', $content)
            ->with('success', 'Episode created successfully.');
    }

    /**
     * Generate vidsrc.icu embed URL for TV shows
     */
    private function generateVidsrcTvUrl($tmdbId, $season = 1, $episode = 1)
    {
        return "https://vidsrc.icu/embed/tv/{$tmdbId}/{$season}/{$episode}";
    }

    /**
     * Add vidsrc embed server to an episode
     */
    private function addVidsrcEmbedToEpisode(Episode $episode, $tmdbId, $season = 1)
    {
        if (!$tmdbId) {
            return;
        }

        // Check if vidsrc server already exists for this episode
        $existingServer = EpisodeServer::where('episode_id', $episode->id)
            ->where('server_name', 'Vidsrc.icu')
            ->first();

        if ($existingServer) {
            return; // Already exists
        }

        $embedUrl = $this->generateVidsrcTvUrl($tmdbId, $season, $episode->episode_number);

        // Get current max sort_order for this episode
        $maxSortOrder = EpisodeServer::where('episode_id', $episode->id)
            ->max('sort_order') ?? 0;

        EpisodeServer::create([
            'episode_id' => $episode->id,
            'server_name' => 'Vidsrc.icu',
            'quality' => 'HD',
            'watch_link' => $embedUrl,
            'download_link' => null,
            'sort_order' => $maxSortOrder + 1,
            'is_active' => true,
        ]);
    }

    /**
     * Generate vidlink.pro embed URL for TV shows
     */
    private function generateVidlinkTvUrl($tmdbId, $season = 1, $episode = 1)
    {
        return "https://vidlink.pro/tv/{$tmdbId}/{$season}/{$episode}";
    }

    /**
     * Add vidlink.pro embed server to an episode
     */
    private function addVidlinkEmbedToEpisode(Episode $episode, $tmdbId, $season = 1)
    {
        if (!$tmdbId) {
            return;
        }

        // Check if vidlink server already exists for this episode
        $existingServer = EpisodeServer::where('episode_id', $episode->id)
            ->where('server_name', 'Vidlink.pro')
            ->first();

        if ($existingServer) {
            return; // Already exists
        }

        $embedUrl = $this->generateVidlinkTvUrl($tmdbId, $season, $episode->episode_number);

        // Get current max sort_order for this episode
        $maxSortOrder = EpisodeServer::where('episode_id', $episode->id)
            ->max('sort_order') ?? 0;

        EpisodeServer::create([
            'episode_id' => $episode->id,
            'server_name' => 'Vidlink.pro',
            'quality' => 'HD',
            'watch_link' => $embedUrl,
            'download_link' => null,
            'sort_order' => $maxSortOrder + 1,
            'is_active' => true,
        ]);
    }

    /**
     * Show the form for editing the specified episode.
     */
    public function edit(Content $content, Episode $episode)
    {
        return view('admin.episodes.edit', compact('content', 'episode'));
    }

    /**
     * Update the specified episode.
     */
    public function update(Request $request, Content $content, Episode $episode)
    {
        $validated = $request->validate([
            'episode_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail_path' => 'nullable|string',
            'air_date' => 'nullable|date',
            'duration' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_published'] = $request->has('is_published') ? true : false;

        // Check if episode number already exists (excluding current episode)
        $existingEpisode = Episode::where('content_id', $content->id)
            ->where('episode_number', $validated['episode_number'])
            ->where('id', '!=', $episode->id)
            ->first();
        
        if ($existingEpisode) {
            return back()->withInput()->withErrors(['episode_number' => 'Episode number already exists for this content.']);
        }

        $episode->update($validated);

        return redirect()->route('admin.episodes.index', $content)
            ->with('success', 'Episode updated successfully.');
    }

    /**
     * Remove the specified episode.
     */
    public function destroy(Content $content, Episode $episode)
    {
        $episode->delete();

        return redirect()->route('admin.episodes.index', $content)
            ->with('success', 'Episode deleted successfully.');
    }

    /**
     * Import episodes from TMDB
     */
    public function importFromTmdb(Request $request, Content $content)
    {
        if (!$content->tmdb_id) {
            return redirect()->back()->with('error', 'Content must have a TMDB ID to import episodes.');
        }

        $validated = $request->validate([
            'season_number' => 'required|integer|min:1',
            'embed_servers' => 'nullable|array',
            'embed_servers.*' => 'nullable|string|in:vidsrc,vidlink',
        ]);

        $seasonNumber = $validated['season_number'];
        $embedServers = $request->input('embed_servers', []);

        // Get season details from TMDB
        $seasonData = $this->tmdb->getTvShowSeason($content->tmdb_id, $seasonNumber);

        if (!$seasonData || !isset($seasonData['episodes'])) {
            return redirect()->back()->with('error', 'Failed to fetch season data from TMDB or season does not exist.');
        }

        $importedCount = 0;
        $skippedCount = 0;

        foreach ($seasonData['episodes'] as $tmdbEpisode) {
            $episodeNumber = $tmdbEpisode['episode_number'] ?? null;
            
            if (!$episodeNumber) {
                continue; // Skip if no episode number
            }

            // Check if episode already exists
            $existingEpisode = Episode::where('content_id', $content->id)
                ->where('episode_number', $episodeNumber)
                ->first();

            if ($existingEpisode) {
                $skippedCount++;
                continue; // Skip existing episodes
            }

            // Prepare episode data
            $episodeData = [
                'content_id' => $content->id,
                'episode_number' => $episodeNumber,
                'title' => $tmdbEpisode['name'] ?? "Episode {$episodeNumber}",
                'description' => $tmdbEpisode['overview'] ?? null,
                'is_published' => true,
                'sort_order' => $episodeNumber,
            ];

            // Set air date if available
            if (!empty($tmdbEpisode['air_date'])) {
                try {
                    $episodeData['air_date'] = Carbon::parse($tmdbEpisode['air_date'])->format('Y-m-d');
                } catch (\Exception $e) {
                    // Invalid date, skip
                }
            }

            // Set thumbnail path from TMDB
            if (!empty($tmdbEpisode['still_path'])) {
                // Store the TMDB path - it will be resolved when displayed
                $episodeData['thumbnail_path'] = $tmdbEpisode['still_path'];
            }

            // Set duration if available
            if (!empty($tmdbEpisode['runtime'])) {
                $episodeData['duration'] = $tmdbEpisode['runtime'];
            }

            // Create episode
            $episode = Episode::create($episodeData);

            // Add embed servers if requested
            if (in_array('vidsrc', $embedServers)) {
                $this->addVidsrcEmbedToEpisode($episode, $content->tmdb_id, $seasonNumber);
            }
            if (in_array('vidlink', $embedServers)) {
                $this->addVidlinkEmbedToEpisode($episode, $content->tmdb_id, $seasonNumber);
            }

            $importedCount++;
        }

        $message = "Successfully imported {$importedCount} episode(s) from season {$seasonNumber}.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} episode(s) were skipped (already exist).";
        }
        
        if (!empty($embedServers)) {
            $serversList = [];
            if (in_array('vidsrc', $embedServers)) {
                $serversList[] = 'Vidsrc.icu';
            }
            if (in_array('vidlink', $embedServers)) {
                $serversList[] = 'Vidlink.pro';
            }
            if (!empty($serversList)) {
                $serversText = implode(' and ', $serversList);
                $message .= " {$serversText} embed servers have been added to imported episodes.";
            }
        }

        return redirect()->route('admin.episodes.index', $content)
            ->with('success', $message);
    }
}

