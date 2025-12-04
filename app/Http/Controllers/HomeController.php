<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Cast;
use App\Services\TmdbService;
use App\Services\SeoService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $tmdb;
    protected $seo;

    public function __construct(TmdbService $tmdb, SeoService $seo)
    {
        $this->tmdb = $tmdb;
        $this->seo = $seo;
    }

    public function index(Request $request)
    {
        $page = (int)$request->get('page', 1);
        $perPage = 20; // Items per page

        // Get all custom content (published only)
        $allCustomContent = Content::published()
            ->get();

        // Convert custom content to array format
        $customContentArray = [];
        foreach ($allCustomContent as $content) {
            $customContentArray[] = [
                'type' => in_array($content->type, ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']) ? 'tv' : 'movie',
                'id' => $content->slug ?? ('custom_' . $content->id),
                'slug' => $content->slug,
                'title' => $content->title,
                'date' => $content->release_date ? $content->release_date->format('Y-m-d') : null,
                'updated_at' => $content->updated_at ? $content->updated_at->format('Y-m-d H:i:s') : null,
                'created_at' => $content->created_at ? $content->created_at->format('Y-m-d H:i:s') : null,
                'rating' => $content->rating ?? 0,
                'backdrop' => $content->backdrop_path ?? $content->poster_path ?? null,
                'poster' => $content->poster_path ?? null,
                'overview' => $content->description ?? '',
                'is_custom' => true,
                'content_id' => $content->id,
                'content_type' => $content->content_type ?? 'custom',
                'content_type_name' => $content->type,
                'dubbing_language' => $content->dubbing_language,
                'is_article' => ($content->content_type ?? 'custom') === 'article',
                'sort_order' => $content->sort_order ?? 0,
            ];
        }

        // Sort with priority:
        // 1. Articles first (content_type = 'article'), sorted by upload time (created_at)
        // 2. Then other content, sorted by upload time (created_at)
        // 3. Most recent uploads (created_at) show first
        // 4. Then by updated_at, then sort_order
        usort($customContentArray, function($a, $b) {
            $aIsArticle = $a['is_article'] ?? false;
            $bIsArticle = $b['is_article'] ?? false;
            
            // Priority 1: Articles always on top
            if ($aIsArticle && !$bIsArticle) {
                return -1; // $a (article) comes first
            }
            if (!$aIsArticle && $bIsArticle) {
                return 1; // $b (article) comes first
            }
            
            // If both are articles OR both are not articles, sort by upload time (created_at) first
            // Priority 2: Sort by created_at (upload time) - most recent uploads first
            $createdA = $a['created_at'] ?? '1970-01-01 00:00:00';
            $createdB = $b['created_at'] ?? '1970-01-01 00:00:00';
            
            $createdTimestampA = strtotime($createdA);
            $createdTimestampB = strtotime($createdB);
            
            // Compare upload timestamps (higher = more recent upload)
            if ($createdTimestampB != $createdTimestampA) {
                return $createdTimestampB <=> $createdTimestampA; // Descending order (newest upload first)
            }
            
            // Priority 3: If created_at is same, sort by updated_at (most recent update first)
            $updatedA = $a['updated_at'] ?? '1970-01-01 00:00:00';
            $updatedB = $b['updated_at'] ?? '1970-01-01 00:00:00';
            
            $updatedTimestampA = strtotime($updatedA);
            $updatedTimestampB = strtotime($updatedB);
            
            if ($updatedTimestampB != $updatedTimestampA) {
                return $updatedTimestampB <=> $updatedTimestampA; // Descending order (newest update first)
            }
            
            // Priority 4: Finally by sort_order (ascending)
            $sortA = $a['sort_order'] ?? 0;
            $sortB = $b['sort_order'] ?? 0;
            return $sortA <=> $sortB;
        });

        // Paginate custom content after sorting
        $totalContentCount = count($customContentArray);
        $totalPages = max(1, ceil($totalContentCount / $perPage));
        
        // Get items for current page
        $startIndex = ($page - 1) * $perPage;
        $allContent = array_slice($customContentArray, $startIndex, $perPage);

        // Get popular content based on views for sidebar
        $popularContent = Content::published()
            ->orderBy('views', 'desc')
            ->orderBy('release_date', 'desc')
            ->take(5)
            ->get();

        // Get popular cast members (with most content)
        $popularCasts = Cast::withCount('contents')
            ->having('contents_count', '>', 0)
            ->orderBy('contents_count', 'desc')
            ->orderBy('name', 'asc')
            ->take(12)
            ->get();

        return view('home', [
            'allContent' => $allContent,
            'currentPage' => $page,
            'totalPages' => max(1, $totalPages),
            'popularContent' => $popularContent,
            'popularCasts' => $popularCasts,
            'seo' => $this->seo->forHome(),
        ]);
    }
}
