<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Episode;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function index()
    {
        return view('admin.analytics.index');
    }

    /**
     * Get overview statistics.
     */
    public function overview(Request $request)
    {
        $period = $request->input('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);

        $data = [
            'total_content' => Content::count(),
            'total_movies' => Content::whereIn('type', ['movie', 'documentary', 'short_film'])->count(),
            'total_tv_shows' => Content::whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])->count(),
            'total_episodes' => Episode::count(),
            'total_views' => Content::sum('views') ?? 0,
            'total_comments' => Comment::where('status', 'approved')->count(),
            'published_content' => Content::where('status', 'published')->count(),
            'draft_content' => Content::where('status', 'draft')->count(),
            
            // Period-specific stats
            'content_added_period' => Content::where('created_at', '>=', $startDate)->count(),
            'views_period' => Content::where('updated_at', '>=', $startDate)->sum('views') ?? 0,
            'episodes_added_period' => Episode::where('created_at', '>=', $startDate)->count(),
            'comments_period' => Comment::where('created_at', '>=', $startDate)->where('status', 'approved')->count(),
        ];

        return response()->json($data);
    }

    /**
     * Get views over time data for charts.
     */
    public function viewsOverTime(Request $request)
    {
        $period = $request->input('period', '30'); // days
        $groupBy = $request->input('group_by', 'day'); // day, week, month
        $startDate = Carbon::now()->subDays($period);

        // Get daily views data
        $data = DB::table('contents')
            ->select(
                DB::raw("DATE(updated_at) as date"),
                DB::raw("SUM(views) as total_views")
            )
            ->where('updated_at', '>=', $startDate)
            ->where('views', '>', 0)
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->orderBy('date', 'asc')
            ->get();

        $labels = [];
        $views = [];
        
        // Create a map of views by date
        $viewsMap = [];
        foreach ($data as $item) {
            $dateKey = $item->date;
            $viewsMap[$dateKey] = (int)($item->total_views ?? 0);
        }
        
        // Generate labels and views for the entire period
        $currentDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::now()->startOfDay();
        
        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('M d');
            $views[] = $viewsMap[$dateKey] ?? 0;
            $currentDate->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'views' => $views,
        ]);
    }

    /**
     * Get content growth over time.
     */
    public function contentGrowth(Request $request)
    {
        $period = $request->input('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);

        $query = Content::select(
            DB::raw("DATE(created_at) as date"),
            DB::raw("COUNT(*) as count")
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date', 'asc');

        $data = $query->get();

        $labels = [];
        $movies = [];
        $tvShows = [];

        foreach ($data as $item) {
            $date = Carbon::parse($item->date);
            $labels[] = $date->format('M d');
            
            $moviesCount = Content::whereDate('created_at', $date)
                ->whereIn('type', ['movie', 'documentary', 'short_film'])
                ->count();
            
            $tvShowsCount = Content::whereDate('created_at', $date)
                ->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])
                ->count();
            
            $movies[] = $moviesCount;
            $tvShows[] = $tvShowsCount;
        }

        return response()->json([
            'labels' => $labels,
            'movies' => $movies,
            'tv_shows' => $tvShows,
        ]);
    }

    /**
     * Get top performing content.
     */
    public function topContent(Request $request)
    {
        $type = $request->input('type', 'all'); // all, movies, tv_shows
        $limit = $request->input('limit', 10);
        $metric = $request->input('metric', 'views'); // views, rating, recent

        $query = Content::query();

        if ($type === 'movies') {
            $query->whereIn('type', ['movie', 'documentary', 'short_film']);
        } elseif ($type === 'tv_shows') {
            $query->whereIn('type', ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']);
        }

        if ($metric === 'views') {
            $query->orderBy('views', 'desc')
                ->where('views', '>', 0);
        } elseif ($metric === 'rating') {
            $query->orderBy('rating', 'desc')
                ->where('rating', '>', 0);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $content = $query->limit($limit)->get();

        $data = $content->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'type' => $item->type,
                'views' => $item->views ?? 0,
                'rating' => $item->rating ?? 0,
                'status' => $item->status,
                'created_at' => $item->created_at->format('Y-m-d'),
                'url' => route('admin.contents.edit', $item),
            ];
        });

        return response()->json($data);
    }

    /**
     * Get content by type breakdown.
     */
    public function contentByType()
    {
        $data = Content::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        $labels = [];
        $counts = [];
        $colors = [
            'movie' => '#3B82F6',
            'tv_show' => '#10B981',
            'web_series' => '#F59E0B',
            'anime' => '#EF4444',
            'documentary' => '#8B5CF6',
            'short_film' => '#EC4899',
            'reality_show' => '#06B6D4',
            'talk_show' => '#84CC16',
        ];

        foreach ($data as $item) {
            $labels[] = ucfirst(str_replace('_', ' ', $item->type));
            $counts[] = $item->count;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $counts,
            'colors' => array_values($colors),
        ]);
    }

    /**
     * Get views by content type.
     */
    public function viewsByType()
    {
        $data = Content::select('type', DB::raw('SUM(views) as total_views'))
            ->where('views', '>', 0)
            ->groupBy('type')
            ->orderBy('total_views', 'desc')
            ->get();

        $labels = [];
        $views = [];

        foreach ($data as $item) {
            $labels[] = ucfirst(str_replace('_', ' ', $item->type));
            $views[] = (int)$item->total_views;
        }

        return response()->json([
            'labels' => $labels,
            'views' => $views,
        ]);
    }

    /**
     * Get status breakdown.
     */
    public function statusBreakdown()
    {
        $data = Content::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $labels = [];
        $counts = [];

        foreach ($data as $item) {
            $labels[] = ucfirst($item->status);
            $counts[] = $item->count;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $counts,
        ]);
    }

    /**
     * Get content source breakdown (TMDB vs Custom).
     */
    public function sourceBreakdown()
    {
        $data = Content::select('content_type', DB::raw('count(*) as count'))
            ->groupBy('content_type')
            ->get();

        $labels = [];
        $counts = [];

        foreach ($data as $item) {
            $labels[] = strtoupper($item->content_type);
            $counts[] = $item->count;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $counts,
        ]);
    }

    /**
     * Get episode analytics.
     */
    public function episodeAnalytics(Request $request)
    {
        $period = $request->input('period', '30');
        $startDate = Carbon::now()->subDays($period);

        $data = [
            'total_episodes' => Episode::count(),
            'published_episodes' => Episode::where('is_published', true)->count(),
            'unpublished_episodes' => Episode::where('is_published', false)->count(),
            'episodes_added_period' => Episode::where('created_at', '>=', $startDate)->count(),
            'total_views' => Episode::sum('views') ?? 0,
        ];

        // Episodes by content
        $episodesByContent = DB::table('episodes')
            ->join('contents', 'episodes.content_id', '=', 'contents.id')
            ->select('contents.title', DB::raw('COUNT(episodes.id) as episode_count'))
            ->groupBy('contents.id', 'contents.title')
            ->orderBy('episode_count', 'desc')
            ->limit(10)
            ->get();

        $data['top_content_by_episodes'] = $episodesByContent;

        return response()->json($data);
    }

    /**
     * Get comments analytics.
     */
    public function commentsAnalytics(Request $request)
    {
        $period = $request->input('period', '30');
        $startDate = Carbon::now()->subDays($period);

        $data = [
            'total_comments' => Comment::count(),
            'approved_comments' => Comment::where('status', 'approved')->count(),
            'pending_comments' => Comment::where('status', 'pending')->count(),
            'rejected_comments' => Comment::where('status', 'rejected')->count(),
            'spam_comments' => Comment::where('status', 'spam')->count(),
            'comments_period' => Comment::where('created_at', '>=', $startDate)->count(),
            'total_likes' => Comment::sum('likes') ?? 0,
            'total_dislikes' => Comment::sum('dislikes') ?? 0,
        ];

        // Comments over time
        $commentsOverTime = Comment::select(
            DB::raw("DATE(created_at) as date"),
            DB::raw("COUNT(*) as count")
        )
        ->where('created_at', '>=', $startDate)
        ->where('status', 'approved')
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        $labels = [];
        $counts = [];

        foreach ($commentsOverTime as $item) {
            $labels[] = Carbon::parse($item->date)->format('M d');
            $counts[] = (int)$item->count;
        }

        $data['comments_over_time'] = [
            'labels' => $labels,
            'data' => $counts,
        ];

        return response()->json($data);
    }

    /**
     * Get daily statistics for the last N days.
     */
    public function dailyStats(Request $request)
    {
        $days = $request->input('days', 30);
        $startDate = Carbon::now()->subDays($days);

        $stats = [];
        
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $nextDate = $date->copy()->addDay();

            $stats[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('M d'),
                'content_added' => Content::whereBetween('created_at', [$date, $nextDate])->count(),
                'episodes_added' => Episode::whereBetween('created_at', [$date, $nextDate])->count(),
                'views' => Content::whereBetween('updated_at', [$date, $nextDate])->sum('views') ?? 0,
                'comments' => Comment::whereBetween('created_at', [$date, $nextDate])->where('status', 'approved')->count(),
            ];
        }

        return response()->json($stats);
    }
}
