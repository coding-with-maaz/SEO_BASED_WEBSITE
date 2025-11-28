<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TmdbService
{
    protected $baseUrl;
    protected $apiKey;
    protected $accessToken;
    protected $imageBaseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.base_url');
        $this->apiKey = config('services.tmdb.api_key');
        $this->accessToken = config('services.tmdb.access_token');
        $this->imageBaseUrl = config('services.tmdb.image_base_url');
    }

    /**
     * Make a request to TMDB API
     */
    protected function request(string $endpoint, array $params = [])
    {
        $params['api_key'] = $this->apiKey;
        
        $cacheKey = 'tmdb_' . md5($endpoint . serialize($params));
        
        return Cache::remember($cacheKey, 3600, function () use ($endpoint, $params) {
            $response = Http::withOptions([
                'verify' => env('APP_ENV') === 'production' ? true : false, // Disable SSL verification in development
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        });
    }

    /**
     * Get popular movies
     */
    public function getPopularMovies($page = 1)
    {
        return $this->request('/movie/popular', ['page' => $page]);
    }

    /**
     * Get top rated movies
     */
    public function getTopRatedMovies($page = 1)
    {
        return $this->request('/movie/top_rated', ['page' => $page]);
    }

    /**
     * Get now playing movies
     */
    public function getNowPlayingMovies($page = 1)
    {
        return $this->request('/movie/now_playing', ['page' => $page]);
    }

    /**
     * Get upcoming movies
     */
    public function getUpcomingMovies($page = 1)
    {
        return $this->request('/movie/upcoming', ['page' => $page]);
    }

    /**
     * Get movie details
     */
    public function getMovieDetails($movieId)
    {
        return $this->request("/movie/{$movieId}", [
            'append_to_response' => 'videos,credits,images,recommendations'
        ]);
    }

    /**
     * Get popular TV shows
     */
    public function getPopularTvShows($page = 1)
    {
        return $this->request('/tv/popular', ['page' => $page]);
    }

    /**
     * Get top rated TV shows
     */
    public function getTopRatedTvShows($page = 1)
    {
        return $this->request('/tv/top_rated', ['page' => $page]);
    }

    /**
     * Get TV show details
     */
    public function getTvShowDetails($tvId)
    {
        return $this->request("/tv/{$tvId}", [
            'append_to_response' => 'videos,credits,images,recommendations'
        ]);
    }

    /**
     * Search movies and TV shows
     */
    public function search($query, $page = 1)
    {
        $movies = $this->request('/search/movie', ['query' => $query, 'page' => $page]);
        $tvShows = $this->request('/search/tv', ['query' => $query, 'page' => $page]);
        
        return [
            'movies' => $movies,
            'tv_shows' => $tvShows,
        ];
    }

    /**
     * Get image URL
     * 
     * Available sizes:
     * - Poster: w92, w154, w185, w342, w500, w780, original
     * - Backdrop: w300, w780, w1280, original
     * - Profile: w45, w185, h632, original
     * - Logo: w45, w92, w154, w185, w300, w500, original
     * - Still: w92, w185, w300, original
     * 
     * @param string|null $path Image path from TMDB
     * @param string $size Image size (default: w500 for posters)
     * @return string Full image URL
     */
    public function getImageUrl($path, $size = 'w500')
    {
        if (!$path) {
            return asset('images/placeholder.jpg');
        }
        
        return $this->imageBaseUrl . '/' . $size . $path;
    }

    /**
     * Get genres
     */
    public function getMovieGenres()
    {
        return $this->request('/genre/movie/list');
    }

    public function getTvGenres()
    {
        return $this->request('/genre/tv/list');
    }
}

