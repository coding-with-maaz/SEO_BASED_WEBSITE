<?php

namespace App\Services;

use App\Models\Content;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Get similar content based on genres, cast, director, etc.
     */
    public function getSimilarContent(Content $content, int $limit = 10): array
    {
        $query = Content::published()
            ->where('id', '!=', $content->id)
            ->whereIn('type', $this->getCompatibleTypes($content->type));

        // Build similarity conditions
        $hasConditions = false;
        $query->where(function($q) use ($content, &$hasConditions) {
            // Match by genres (if available)
            if ($content->genres && is_array($content->genres) && count($content->genres) > 0) {
                $genres = $this->extractGenreNames($content->genres);
                if (!empty($genres)) {
                    $hasConditions = true;
                    $q->where(function($subQ) use ($genres) {
                        foreach ($genres as $genre) {
                            $subQ->orWhereJsonContains('genres', $genre)
                              ->orWhereJsonContains('genres', ['name' => $genre])
                              ->orWhere('genres', 'like', '%' . $genre . '%');
                        }
                    });
                }
            }

            // Match by director (if available)
            if ($content->director) {
                $hasConditions = true;
                $q->orWhere('director', $content->director);
            }

            // Match by country (if available)
            if ($content->country) {
                $hasConditions = true;
                $q->orWhere('country', $content->country);
            }

            // Match by cast members
            if ($content->castMembers && $content->castMembers->count() > 0) {
                $hasConditions = true;
                $castIds = $content->castMembers->pluck('id')->toArray();
                $q->orWhereHas('castMembers', function($subQ) use ($castIds) {
                    $subQ->whereIn('casts.id', $castIds);
                });
            }
        });

        // If no similarity conditions, just get popular content of same type
        if (!$hasConditions) {
            $query = Content::published()
                ->where('id', '!=', $content->id)
                ->whereIn('type', $this->getCompatibleTypes($content->type));
        }

        // Order by relevance (genre match, then rating, then views)
        $results = $query->orderBy('rating', 'desc')
            ->orderBy('views', 'desc')
            ->orderBy('release_date', 'desc')
            ->limit($limit)
            ->get();

        return $this->formatForView($results);
    }

    /**
     * Get trending content (based on views in last 7 days, rating, etc.)
     */
    public function getTrendingContent(string $type = 'all', int $limit = 10): array
    {
        $query = Content::published();

        if ($type !== 'all') {
            $types = $this->getTypeGroup($type);
            $query->whereIn('type', $types);
        }

        // Trending algorithm: high views + high rating + recent release
        $results = $query->orderBy('views', 'desc')
            ->orderBy('rating', 'desc')
            ->orderBy('release_date', 'desc')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        return $this->formatForView($results);
    }

    /**
     * Get "You may also like" based on viewing history
     */
    public function getYouMayAlsoLike(string $type = 'all', int $limit = 10): array
    {
        $viewingHistory = $this->getViewingHistory();
        
        if (empty($viewingHistory)) {
            // If no viewing history, return trending content
            return $this->getTrendingContent($type, $limit);
        }

        $query = Content::published()
            ->whereNotIn('id', $viewingHistory);

        if ($type !== 'all') {
            $types = $this->getTypeGroup($type);
            $query->whereIn('type', $types);
        }

        // Get genres from viewing history
        $historyGenres = $this->getGenresFromHistory($viewingHistory);
        
        if (!empty($historyGenres)) {
            $query->where(function($q) use ($historyGenres) {
                foreach ($historyGenres as $genre) {
                    $q->orWhereJsonContains('genres', $genre)
                      ->orWhereJsonContains('genres', ['name' => $genre])
                      ->orWhere('genres', 'like', '%' . $genre . '%');
                }
            });
        }

        $results = $query->orderBy('rating', 'desc')
            ->orderBy('views', 'desc')
            ->orderBy('release_date', 'desc')
            ->limit($limit)
            ->get();

        // If not enough results, fill with trending
        if ($results->count() < $limit) {
            $trending = $this->getTrendingContent($type, $limit - $results->count());
            $formatted = $this->formatForView($results);
            return array_merge($formatted, $trending);
        }

        return $this->formatForView($results);
    }

    /**
     * Get personalized recommendations based on user preferences
     */
    public function getPersonalizedRecommendations(string $type = 'all', int $limit = 10): array
    {
        $viewingHistory = $this->getViewingHistory();
        
        if (empty($viewingHistory)) {
            return $this->getTrendingContent($type, $limit);
        }

        // Analyze viewing history to determine preferences
        $preferredGenres = $this->getGenresFromHistory($viewingHistory);
        $preferredCast = $this->getCastFromHistory($viewingHistory);
        $preferredDirectors = $this->getDirectorsFromHistory($viewingHistory);

        $query = Content::published()
            ->whereNotIn('id', $viewingHistory);

        if ($type !== 'all') {
            $types = $this->getTypeGroup($type);
            $query->whereIn('type', $types);
        }

        // Build personalized query
        $query->where(function($q) use ($preferredGenres, $preferredCast, $preferredDirectors) {
            // Match genres
            if (!empty($preferredGenres)) {
                foreach ($preferredGenres as $genre) {
                    $q->orWhereJsonContains('genres', $genre)
                      ->orWhereJsonContains('genres', ['name' => $genre])
                      ->orWhere('genres', 'like', '%' . $genre . '%');
                }
            }

            // Match cast
            if (!empty($preferredCast)) {
                $q->orWhereHas('castMembers', function($subQ) use ($preferredCast) {
                    $subQ->whereIn('casts.id', $preferredCast);
                });
            }

            // Match directors
            if (!empty($preferredDirectors)) {
                $q->orWhereIn('director', $preferredDirectors);
            }
        });

        $results = $query->orderBy('rating', 'desc')
            ->orderBy('views', 'desc')
            ->orderBy('release_date', 'desc')
            ->limit($limit)
            ->get();

        // Fill with trending if needed
        if ($results->count() < $limit) {
            $trending = $this->getTrendingContent($type, $limit - $results->count());
            $formatted = $this->formatForView($results);
            return array_merge($formatted, $trending);
        }

        return $this->formatForView($results);
    }

    /**
     * Track content view in session
     */
    public function trackView(int $contentId): void
    {
        $history = $this->getViewingHistory();
        
        // Remove if already exists (to move to front)
        $history = array_filter($history, fn($id) => $id != $contentId);
        
        // Add to front
        array_unshift($history, $contentId);
        
        // Keep only last 20 items
        $history = array_slice($history, 0, 20);
        
        Session::put('viewing_history', $history);
    }

    /**
     * Get viewing history from session
     */
    private function getViewingHistory(): array
    {
        return Session::get('viewing_history', []);
    }

    /**
     * Get genres from viewing history
     */
    private function getGenresFromHistory(array $history): array
    {
        if (empty($history)) {
            return [];
        }

        $contents = Content::whereIn('id', array_slice($history, 0, 10))
            ->whereNotNull('genres')
            ->get();

        $genres = [];
        foreach ($contents as $content) {
            if ($content->genres && is_array($content->genres)) {
                $extracted = $this->extractGenreNames($content->genres);
                $genres = array_merge($genres, $extracted);
            }
        }

        // Get most common genres
        $genreCounts = array_count_values($genres);
        arsort($genreCounts);
        
        return array_slice(array_keys($genreCounts), 0, 5);
    }

    /**
     * Get cast IDs from viewing history
     */
    private function getCastFromHistory(array $history): array
    {
        if (empty($history)) {
            return [];
        }

        $contents = Content::whereIn('id', array_slice($history, 0, 10))
            ->with('castMembers')
            ->get();

        $castIds = [];
        foreach ($contents as $content) {
            if ($content->castMembers) {
                $castIds = array_merge($castIds, $content->castMembers->pluck('id')->toArray());
            }
        }

        // Get most common cast
        $castCounts = array_count_values($castIds);
        arsort($castCounts);
        
        return array_slice(array_keys($castCounts), 0, 5);
    }

    /**
     * Get directors from viewing history
     */
    private function getDirectorsFromHistory(array $history): array
    {
        if (empty($history)) {
            return [];
        }

        $directors = Content::whereIn('id', array_slice($history, 0, 10))
            ->whereNotNull('director')
            ->pluck('director')
            ->filter()
            ->toArray();

        $directorCounts = array_count_values($directors);
        arsort($directorCounts);
        
        return array_slice(array_keys($directorCounts), 0, 3);
    }

    /**
     * Extract genre names from genres array
     */
    private function extractGenreNames(array $genres): array
    {
        $names = [];
        foreach ($genres as $genre) {
            if (is_array($genre)) {
                $names[] = $genre['name'] ?? $genre;
            } else {
                $names[] = $genre;
            }
        }
        return array_filter($names);
    }

    /**
     * Get compatible content types
     */
    private function getCompatibleTypes(string $type): array
    {
        $typeGroups = [
            'movie' => ['movie', 'documentary', 'short_film'],
            'tv_show' => ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'],
            'documentary' => ['movie', 'documentary'],
            'short_film' => ['movie', 'short_film'],
            'web_series' => ['tv_show', 'web_series', 'anime'],
            'anime' => ['tv_show', 'web_series', 'anime'],
            'reality_show' => ['tv_show', 'reality_show', 'talk_show'],
            'talk_show' => ['tv_show', 'reality_show', 'talk_show'],
        ];

        return $typeGroups[$type] ?? [$type];
    }

    /**
     * Get type group for filtering
     */
    private function getTypeGroup(string $type): array
    {
        if ($type === 'movies') {
            return ['movie', 'documentary', 'short_film'];
        } elseif ($type === 'tv_shows') {
            return ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'];
        }
        return [$type];
    }

    /**
     * Format content for view
     */
    private function formatForView($contents): array
    {
        return $contents->map(function($content) {
            return [
                'id' => $content->slug ?? ('custom_' . $content->id),
                'title' => $content->title,
                'name' => $content->title,
                'release_date' => $content->release_date ? $content->release_date->format('Y-m-d') : null,
                'first_air_date' => $content->release_date ? $content->release_date->format('Y-m-d') : null,
                'poster_path' => $content->poster_path,
                'backdrop_path' => $content->backdrop_path,
                'vote_average' => $content->rating ?? 0,
                'is_custom' => true,
                'content_type' => $content->content_type ?? 'custom',
                'type' => $content->type,
                'views' => $content->views ?? 0,
            ];
        })->toArray();
    }
}

