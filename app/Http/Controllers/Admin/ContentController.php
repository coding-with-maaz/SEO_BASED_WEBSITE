<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Cast;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContentController extends Controller
{
    protected $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Content::query();

        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Type filter
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $contents = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.contents.index', compact('contents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contentTypes = Content::getContentTypes();
        $dubbingLanguages = Content::getDubbingLanguages();
        return view('admin.contents.create', compact('contentTypes', 'dubbingLanguages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'article_content' => 'nullable|string',
            'type' => 'required|string',
            'content_type' => 'required|string|in:custom,tmdb,article',
            'tmdb_id' => 'nullable|string',
            'poster_path' => 'nullable|string',
            'backdrop_path' => 'nullable|string',
            'release_date' => 'nullable|date',
            'rating' => 'nullable|numeric|min:0|max:10',
            'episode_count' => 'nullable|integer|min:0',
            'status' => 'required|string|in:published,draft,upcoming',
            'series_status' => 'nullable|string|in:ongoing,completed,cancelled,upcoming,on_hold',
            'network' => 'nullable|string|max:255',
            'end_date' => 'nullable|date',
            'duration' => 'nullable|integer|min:0',
            'country' => 'nullable|string|max:255',
            'director' => 'nullable|string|max:255',
            'genres' => 'nullable|array',
            'language' => 'nullable|string|max:50',
            'dubbing_language' => 'nullable|string|max:50',
            'download_link' => 'nullable|url',
            'watch_link' => 'nullable|url',
            'servers' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
        ]);

        // Set default values
        $validated['status'] = $validated['status'] ?? 'published';
        $validated['content_type'] = $validated['content_type'] ?? 'custom';
        $validated['is_featured'] = $validated['is_featured'] ?? false;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        
        // Set default rating to 0 if null or empty
        if (empty($validated['rating']) || $validated['rating'] === null) {
            $validated['rating'] = 0;
        }

        Content::create($validated);

        return redirect()->route('admin.contents.index')
            ->with('success', 'Content created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Content $content)
    {
        return view('admin.contents.show', compact('content'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $content)
    {
        $contentTypes = Content::getContentTypes();
        $dubbingLanguages = Content::getDubbingLanguages();
        return view('admin.contents.edit', compact('content', 'contentTypes', 'dubbingLanguages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $content)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'article_content' => 'nullable|string',
            'type' => 'required|string',
            'content_type' => 'required|string|in:custom,tmdb,article',
            'tmdb_id' => 'nullable|string',
            'poster_path' => 'nullable|string',
            'backdrop_path' => 'nullable|string',
            'release_date' => 'nullable|date',
            'rating' => 'nullable|numeric|min:0|max:10',
            'episode_count' => 'nullable|integer|min:0',
            'status' => 'required|string|in:published,draft,upcoming',
            'series_status' => 'nullable|string|in:ongoing,completed,cancelled,upcoming,on_hold',
            'network' => 'nullable|string|max:255',
            'end_date' => 'nullable|date',
            'duration' => 'nullable|integer|min:0',
            'country' => 'nullable|string|max:255',
            'director' => 'nullable|string|max:255',
            'genres' => 'nullable|array',
            'language' => 'nullable|string|max:50',
            'dubbing_language' => 'nullable|string|max:50',
            'download_link' => 'nullable|url',
            'watch_link' => 'nullable|url',
            'servers' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
        ]);

        // Set default rating to 0 if null or empty (for updates)
        if (array_key_exists('rating', $validated) && (empty($validated['rating']) || $validated['rating'] === null)) {
            $validated['rating'] = 0;
        }

        $content->update($validated);

        return redirect()->route('admin.contents.index')
            ->with('success', 'Content updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        $content->delete();

        return redirect()->route('admin.contents.index')
            ->with('success', 'Content deleted successfully.');
    }

    /**
     * Store article content (TMDB content with backdrop and watch/download buttons)
     */
    public function storeArticleContent(Request $request)
    {
        $validated = $request->validate([
            'tmdb_id' => 'required|integer',
            'tmdb_type' => 'required|string|in:movie,tv',
            'article_content' => 'nullable|string',
            'watch_link' => 'nullable|url',
            'download_link' => 'nullable|url',
            'status' => 'required|string|in:published,draft,upcoming',
        ]);

        $tmdbId = $validated['tmdb_id'];
        $type = $validated['tmdb_type'];

        // Get details from TMDB
        if ($type === 'movie') {
            $tmdbData = $this->tmdb->getMovieDetails($tmdbId);
            $contentType = 'movie';
        } else {
            $tmdbData = $this->tmdb->getTvShowDetails($tmdbId);
            $contentType = 'tv_show';
        }

        if (!$tmdbData) {
            return redirect()->back()->with('error', 'Failed to fetch details from TMDB.');
        }

        // Prepare content data for article
        $contentData = [
            'title' => $tmdbData['title'] ?? ($tmdbData['name'] ?? 'Unknown'),
            'content_type' => 'article',
            'tmdb_id' => (string)$tmdbId,
            'type' => $contentType,
            'description' => $tmdbData['overview'] ?? null,
            'article_content' => $validated['article_content'] ?? null,
            'poster_path' => $tmdbData['poster_path'] ?? null,
            'backdrop_path' => $tmdbData['backdrop_path'] ?? null,
            'rating' => isset($tmdbData['vote_average']) ? round($tmdbData['vote_average'], 1) : 0,
            'status' => $validated['status'],
            'watch_link' => $validated['watch_link'] ?? null,
            'download_link' => $validated['download_link'] ?? null,
        ];

        // Set release date
        $releaseDate = null;
        if (isset($tmdbData['release_date']) && !empty(trim($tmdbData['release_date']))) {
            $releaseDate = $tmdbData['release_date'];
        } elseif (isset($tmdbData['first_air_date']) && !empty(trim($tmdbData['first_air_date']))) {
            $releaseDate = $tmdbData['first_air_date'];
        }
        
        if ($releaseDate) {
            try {
                $parsedDate = Carbon::parse($releaseDate);
                $contentData['release_date'] = $parsedDate->format('Y-m-d');
            } catch (\Exception $e) {
                $contentData['release_date'] = null;
            }
        } else {
            $contentData['release_date'] = null;
        }

        // Set genres
        if (isset($tmdbData['genres']) && is_array($tmdbData['genres'])) {
            $contentData['genres'] = array_column($tmdbData['genres'], 'name');
        }

        // Set director (for movies)
        if ($type === 'movie' && isset($tmdbData['credits']['crew'])) {
            $director = collect($tmdbData['credits']['crew'])->firstWhere('job', 'Director');
            if ($director) {
                $contentData['director'] = $director['name'];
            }
        }

        // Set duration
        if (isset($tmdbData['runtime'])) {
            $contentData['duration'] = $tmdbData['runtime'];
        } elseif (isset($tmdbData['episode_run_time'][0])) {
            $contentData['duration'] = $tmdbData['episode_run_time'][0];
        }

        // Set episode count for TV shows
        if ($type === 'tv' && isset($tmdbData['number_of_episodes'])) {
            $contentData['episode_count'] = $tmdbData['number_of_episodes'];
        }

        // Set series status for TV shows
        if ($type === 'tv' && isset($tmdbData['status'])) {
            $statusMap = [
                'Returning Series' => 'ongoing',
                'Ended' => 'completed',
                'Canceled' => 'cancelled',
                'Planned' => 'upcoming',
            ];
            $contentData['series_status'] = $statusMap[$tmdbData['status']] ?? 'ongoing';
        }

        // Set network
        if (isset($tmdbData['networks'][0]['name'])) {
            $contentData['network'] = $tmdbData['networks'][0]['name'];
        }

        // Set original language
        if (isset($tmdbData['original_language'])) {
            $contentData['language'] = $tmdbData['original_language'];
        }

        // Clean up empty strings
        $nullableFields = ['release_date', 'description', 'article_content', 'poster_path', 'backdrop_path', 'director', 'country', 'network', 'language', 'watch_link', 'download_link'];
        foreach ($nullableFields as $field) {
            if (isset($contentData[$field]) && $contentData[$field] === '') {
                $contentData[$field] = null;
            }
        }

        // Set defaults
        $contentData['sort_order'] = 0;
        $contentData['is_featured'] = false;

        // Check if content already exists
        $existingContent = Content::withTrashed()
            ->where('tmdb_id', $tmdbId)
            ->where('content_type', 'article')
            ->first();

        if ($existingContent) {
            if ($existingContent->trashed()) {
                $existingContent->restore();
            }
            unset($contentData['slug']);
            $existingContent->update($contentData);
            $content = $existingContent;
        } else {
            unset($contentData['slug']);
            $content = Content::create($contentData);
        }

        // Handle cast automatically (same as import)
        if (isset($tmdbData['credits']['cast']) && is_array($tmdbData['credits']['cast']) && count($tmdbData['credits']['cast']) > 0) {
            $tmdbCastAttachments = [];
            $tmdbCastIds = [];
            
            foreach (array_slice($tmdbData['credits']['cast'], 0, 20) as $index => $actor) {
                $actorName = $actor['name'] ?? 'Unknown';
                $character = $actor['character'] ?? '';
                $profilePath = $actor['profile_path'] ?? null;
                
                $fullProfilePath = null;
                if ($profilePath) {
                    if (str_starts_with($profilePath, 'http')) {
                        $fullProfilePath = $profilePath;
                    } else {
                        $fullProfilePath = $this->tmdb->getImageUrl($profilePath, 'w185');
                    }
                }
                
                $cast = Cast::where('name', trim($actorName))->first();
                
                if ($cast) {
                    if (empty($cast->profile_path) && $fullProfilePath) {
                        $cast->update(['profile_path' => $fullProfilePath]);
                    }
                } else {
                    $cast = Cast::create([
                        'name' => trim($actorName),
                        'profile_path' => $fullProfilePath,
                    ]);
                }
                
                $tmdbCastIds[] = $cast->id;
                $tmdbCastAttachments[$cast->id] = [
                    'character' => $character,
                    'order' => $index,
                ];
            }
            
            if (!empty($tmdbCastAttachments)) {
                try {
                    $content->castMembers()->sync($tmdbCastAttachments);
                    $content->refresh();
                } catch (\Exception $e) {
                    \Log::error('Error syncing casts for article content ' . $content->id . ': ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.contents.edit', $content)
            ->with('success', 'Article content created successfully with backdrop image and watch/download buttons.');
    }

    /**
     * Search TMDB for movies/TV shows
     */
    public function searchTmdb(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'movie'); // movie or tv

        if (!$query) {
            return response()->json(['results' => []]);
        }

        if ($type === 'movie') {
            $results = $this->tmdb->searchMovies($query, 1);
        } else {
            $results = $this->tmdb->searchTvShows($query, 1);
        }

        return response()->json([
            'results' => $results['results'] ?? []
        ]);
    }

    /**
     * Get TMDB details for import
     */
    public function getTmdbDetails(Request $request)
    {
        $tmdbId = $request->get('tmdb_id');
        $type = $request->get('type', 'movie');

        if ($type === 'movie') {
            $details = $this->tmdb->getMovieDetails($tmdbId);
        } else {
            $details = $this->tmdb->getTvShowDetails($tmdbId);
        }

        if (!$details) {
            return response()->json(['error' => 'Movie/TV show not found'], 404);
        }

        return response()->json($details);
    }

    /**
     * Import content from TMDB
     */
    public function importFromTmdb(Request $request)
    {
        $validated = $request->validate([
            'tmdb_id' => 'required|integer',
            'type' => 'required|string|in:movie,tv',
            'dubbing_language' => 'nullable|string',
        ]);

        $tmdbId = $validated['tmdb_id'];
        $type = $validated['type'];

        // Get details from TMDB
        if ($type === 'movie') {
            $tmdbData = $this->tmdb->getMovieDetails($tmdbId);
            $contentType = 'movie';
        } else {
            $tmdbData = $this->tmdb->getTvShowDetails($tmdbId);
            $contentType = 'tv_show';
        }

        if (!$tmdbData) {
            return redirect()->back()->with('error', 'Failed to fetch details from TMDB.');
        }

        // Prepare content data
        $contentData = [
            'title' => $tmdbData['title'] ?? ($tmdbData['name'] ?? 'Unknown'),
            'content_type' => 'tmdb',
            'tmdb_id' => (string)$tmdbId,
            'type' => $contentType,
            'description' => $tmdbData['overview'] ?? null,
            'poster_path' => $tmdbData['poster_path'] ?? null,
            'backdrop_path' => $tmdbData['backdrop_path'] ?? null,
            'rating' => isset($tmdbData['vote_average']) ? round($tmdbData['vote_average'], 1) : 0,
            'status' => 'published',
            'dubbing_language' => $validated['dubbing_language'] ?? null,
        ];

        // Set release date (only if valid and not empty)
        $releaseDate = null;
        if (isset($tmdbData['release_date']) && !empty(trim($tmdbData['release_date']))) {
            $releaseDate = $tmdbData['release_date'];
        } elseif (isset($tmdbData['first_air_date']) && !empty(trim($tmdbData['first_air_date']))) {
            $releaseDate = $tmdbData['first_air_date'];
        }
        
        // Validate date format before setting
        if ($releaseDate) {
            try {
                // Try to parse the date to ensure it's valid
                $parsedDate = Carbon::parse($releaseDate);
                $contentData['release_date'] = $parsedDate->format('Y-m-d');
            } catch (\Exception $e) {
                // If date parsing fails, set to null
                $contentData['release_date'] = null;
            }
        } else {
            $contentData['release_date'] = null;
        }

        // Set genres
        if (isset($tmdbData['genres']) && is_array($tmdbData['genres'])) {
            $contentData['genres'] = array_column($tmdbData['genres'], 'name');
        }

        // Don't set cast here - we'll handle it separately after content creation using the relationship

        // Set director (for movies)
        if ($type === 'movie' && isset($tmdbData['credits']['crew'])) {
            $director = collect($tmdbData['credits']['crew'])->firstWhere('job', 'Director');
            if ($director) {
                $contentData['director'] = $director['name'];
            }
        }

        // Set duration
        if (isset($tmdbData['runtime'])) {
            $contentData['duration'] = $tmdbData['runtime'];
        } elseif (isset($tmdbData['episode_run_time'][0])) {
            $contentData['duration'] = $tmdbData['episode_run_time'][0];
        }

        // Set episode count for TV shows
        if ($type === 'tv' && isset($tmdbData['number_of_episodes'])) {
            $contentData['episode_count'] = $tmdbData['number_of_episodes'];
        }

        // Set series status for TV shows
        if ($type === 'tv' && isset($tmdbData['status'])) {
            $statusMap = [
                'Returning Series' => 'ongoing',
                'Ended' => 'completed',
                'Canceled' => 'cancelled',
                'Planned' => 'upcoming',
            ];
            $contentData['series_status'] = $statusMap[$tmdbData['status']] ?? 'ongoing';
        }

        // Set network
        if (isset($tmdbData['networks'][0]['name'])) {
            $contentData['network'] = $tmdbData['networks'][0]['name'];
        }

        // Set original language
        if (isset($tmdbData['original_language'])) {
            $contentData['language'] = $tmdbData['original_language'];
        }

        // Clean up empty strings - convert to null for nullable fields
        $nullableFields = ['release_date', 'description', 'poster_path', 'backdrop_path', 'director', 'country', 'network', 'language', 'dubbing_language'];
        foreach ($nullableFields as $field) {
            if (isset($contentData[$field]) && $contentData[$field] === '') {
                $contentData[$field] = null;
            }
        }

        // Check if content already exists (including soft-deleted)
        $existingContent = Content::withTrashed()
            ->where('tmdb_id', $tmdbId)
            ->where('content_type', 'tmdb')
            ->first();

        if ($existingContent) {
            // If content was soft-deleted, restore it first
            if ($existingContent->trashed()) {
                $existingContent->restore();
            }
            // Update the content with new data (keep existing slug)
            // Don't overwrite slug if content already exists
            unset($contentData['slug']);
            $existingContent->update($contentData);
            $content = $existingContent;
        } else {
            // Create new content (slug will be auto-generated by model's creating event)
            // Make sure slug is not set manually to allow auto-generation with conflict checking
            unset($contentData['slug']);
            $content = Content::create($contentData);
        }

        // Handle cast using the relationship - automatically add from TMDB
        // Flow for each cast:
        // 1. If cast already exists in database → reuse it (include in content)
        // 2. If cast doesn't exist → create new cast, then include it in content
        // This prevents duplicate cast records and ensures all casts are stored in database
        
        // Log TMDB data structure for debugging
        \Log::info('TMDB import - Checking for cast data', [
            'has_credits' => isset($tmdbData['credits']),
            'has_cast' => isset($tmdbData['credits']['cast']),
            'cast_count' => isset($tmdbData['credits']['cast']) ? count($tmdbData['credits']['cast']) : 0,
            'content_id' => $content->id ?? 'not_created_yet',
        ]);
        
        if (isset($tmdbData['credits']['cast']) && is_array($tmdbData['credits']['cast']) && count($tmdbData['credits']['cast']) > 0) {
            $tmdbCastAttachments = [];
            $tmdbCastIds = [];
            
            // Process TMDB cast members (up to 20)
            foreach (array_slice($tmdbData['credits']['cast'], 0, 20) as $index => $actor) {
                $actorName = $actor['name'] ?? 'Unknown';
                $character = $actor['character'] ?? '';
                $profilePath = $actor['profile_path'] ?? null;
                
                // Build full profile URL if profile path exists
                $fullProfilePath = null;
                if ($profilePath) {
                    if (str_starts_with($profilePath, 'http')) {
                        $fullProfilePath = $profilePath;
                    } else {
                        $fullProfilePath = $this->tmdb->getImageUrl($profilePath, 'w185');
                    }
                }
                
                // Step 1: Check if cast already exists in database
                $cast = Cast::where('name', trim($actorName))->first();
                
                if ($cast) {
                    // Cast already exists - reuse it (don't create duplicate)
                    // Update profile path if empty and we have one
                    if (empty($cast->profile_path) && $fullProfilePath) {
                        $cast->update(['profile_path' => $fullProfilePath]);
                    }
                } else {
                    // Cast doesn't exist - create new cast member in database
                    $cast = Cast::create([
                        'name' => trim($actorName),
                        'profile_path' => $fullProfilePath,
                    ]);
                }
                
                // Track TMDB cast IDs
                $tmdbCastIds[] = $cast->id;
                
                // Step 2: Include/attach cast to content with character and order
                // TMDB casts get order 0-19
                $tmdbCastAttachments[$cast->id] = [
                    'character' => $character,
                    'order' => $index,
                ];
            }
            
            // Preserve manually added casts (casts not from TMDB)
            $allCastAttachments = $tmdbCastAttachments;
            
            if ($existingContent) {
                $currentCasts = $content->castMembers()->withPivot('character', 'order')->get();
                $maxTmdbOrder = count($tmdbCastAttachments) > 0 ? max(array_column($tmdbCastAttachments, 'order')) : -1;
                $manualOrderOffset = $maxTmdbOrder + 1;
                
                foreach ($currentCasts as $currentCast) {
                    // If this cast is not in the new TMDB list, preserve it as manually added
                    if (!in_array($currentCast->id, $tmdbCastIds)) {
                        $allCastAttachments[$currentCast->id] = [
                            'character' => $currentCast->pivot->character ?? '',
                            'order' => $currentCast->pivot->order ?? $manualOrderOffset++,
                        ];
                    }
                }
            }
            
            // Sync all casts (TMDB casts + manually added casts)
            if (!empty($allCastAttachments)) {
                try {
                    $content->castMembers()->sync($allCastAttachments);
                    // Refresh the content to ensure relationships are loaded
                    $content->refresh();
                    
                    // Verify casts were synced (for debugging)
                    $syncedCount = $content->castMembers()->count();
                    \Log::info('Synced ' . $syncedCount . ' casts for content ID: ' . $content->id);
                } catch (\Exception $e) {
                    \Log::error('Error syncing casts for content ' . $content->id . ': ' . $e->getMessage());
                    \Log::error('Stack trace: ' . $e->getTraceAsString());
                    // Continue anyway - casts can be added manually later
                }
            } else {
                \Log::warning('No cast attachments to sync for content ID: ' . $content->id);
            }
        } else {
            \Log::info('No TMDB cast data available for content ID: ' . $content->id);
        }

        // Build success message with cast information
        $castCount = 0;
        if (isset($tmdbData['credits']['cast']) && is_array($tmdbData['credits']['cast'])) {
            $castCount = min(count($tmdbData['credits']['cast']), 20);
        }
        
        $successMessage = $existingContent 
            ? 'Content updated from TMDB successfully.' 
            : 'Content imported from TMDB successfully. Please add servers and download links.';
        
        if ($castCount > 0) {
            $successMessage .= ' ' . $castCount . ' cast member(s) have been automatically added.';
        }
        
        return redirect()->route('admin.contents.edit', $content)
            ->with('success', $successMessage);
    }

    /**
     * Add server to content
     */
    public function addServer(Request $request, Content $content)
    {
        $validated = $request->validate([
            'server_name' => 'required|string|max:255',
            'quality' => 'nullable|string|max:50',
            'watch_link' => 'nullable|url',
            'download_link' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $servers = $content->servers ?? [];
        
        // Generate unique ID for server
        $serverId = uniqid('server_', true);
        
        $servers[] = [
            'id' => $serverId,
            'name' => $validated['server_name'],
            'url' => $validated['watch_link'] ?? null,
            'quality' => $validated['quality'] ?? 'HD',
            'download_link' => $validated['download_link'] ?? null,
            'sort_order' => $validated['sort_order'] ?? count($servers),
            'active' => true,
        ];
        
        // Sort servers by sort_order
        usort($servers, function($a, $b) {
            return ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0);
        });

        $content->update(['servers' => $servers]);

        return redirect()->back()->with('success', 'Server added successfully.');
    }

    /**
     * Update server
     */
    public function updateServer(Request $request, Content $content)
    {
        $validated = $request->validate([
            'server_id' => 'required|string',
            'server_name' => 'required|string|max:255',
            'quality' => 'nullable|string|max:50',
            'watch_link' => 'nullable|url',
            'download_link' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $servers = $content->servers ?? [];
        
        foreach ($servers as &$server) {
            if (isset($server['id']) && $server['id'] === $validated['server_id']) {
                $server['name'] = $validated['server_name'];
                $server['url'] = $validated['watch_link'] ?? $server['url'] ?? null;
                $server['quality'] = $validated['quality'] ?? $server['quality'] ?? 'HD';
                $server['download_link'] = $validated['download_link'] ?? $server['download_link'] ?? null;
                $server['sort_order'] = $validated['sort_order'] ?? $server['sort_order'] ?? 0;
                $server['active'] = $validated['is_active'] ?? true;
                break;
            }
        }
        
        // Sort servers by sort_order
        usort($servers, function($a, $b) {
            return ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0);
        });

        $content->update(['servers' => $servers]);

        return redirect()->back()->with('success', 'Server updated successfully.');
    }

    /**
     * Delete server
     */
    public function deleteServer(Request $request, Content $content)
    {
        $serverId = $request->get('server_id');
        
        if (!$serverId) {
            return redirect()->back()->with('error', 'Server ID is required.');
        }

        $servers = $content->servers ?? [];
        $servers = array_filter($servers, function($server) use ($serverId) {
            return !isset($server['id']) || $server['id'] !== $serverId;
        });

        // Re-index array
        $servers = array_values($servers);

        $content->update(['servers' => $servers]);

        return redirect()->back()->with('success', 'Server deleted successfully.');
    }
}
