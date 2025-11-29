<?php

namespace App\Http\Controllers;

use App\Models\Cast;
use App\Models\Content;
use Illuminate\Http\Request;

class CastController extends Controller
{
    /**
     * Display a listing of cast members.
     */
    public function index(Request $request)
    {
        $page = (int)$request->get('page', 1);
        $perPage = 24; // Items per page
        
        // Get all casts with content count
        $query = Cast::withCount('contents');
        
        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Get total count for pagination
        $totalCasts = $query->count();
        $totalPages = max(1, ceil($totalCasts / $perPage));
        
        // Paginate casts
        $casts = $query->orderBy('name', 'asc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return view('cast.index', [
            'casts' => $casts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $request->search ?? '',
        ]);
    }

    /**
     * Display the cast member detail page.
     */
    public function show($slug)
    {
        // Find cast by slug or ID (backward compatibility)
        $cast = null;
        
        // First try by slug
        $cast = Cast::where('slug', $slug)->first();
        
        // If not found and slug is numeric, try by ID
        if (!$cast && is_numeric($slug)) {
            $cast = Cast::find($slug);
        }

        if (!$cast) {
            abort(404);
        }

        // Get all content (movies/TV shows) this cast member is in
        $contents = $cast->contents()
            ->where('status', 'published')
            ->orderBy('release_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Separate movies and TV shows
        $movies = $contents->filter(function($content) {
            return in_array($content->type, ['movie', 'documentary', 'short_film']);
        });

        $tvShows = $contents->filter(function($content) {
            return in_array($content->type, ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']);
        });

        return view('cast.show', [
            'cast' => $cast,
            'movies' => $movies,
            'tvShows' => $tvShows,
        ]);
    }
}

