<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    protected $sitemapService;

    public function __construct(SitemapService $sitemapService)
    {
        $this->sitemapService = $sitemapService;
    }

    /**
     * Generate main sitemap (all URLs in one file)
     */
    public function index(): Response
    {
        $urls = $this->sitemapService->getAllUrlsFlat();
        
        return response()->view('sitemap.index', [
            'urls' => $urls,
        ])->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap index (for multiple sitemap files)
     */
    public function sitemapIndex(): Response
    {
        $sitemaps = $this->sitemapService->getSitemapIndex();
        
        return response()->view('sitemap.index-file', [
            'sitemaps' => $sitemaps,
        ])->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap by type
     */
    public function byType(string $type): Response
    {
        $urls = $this->sitemapService->getSitemapByType($type);
        
        if (empty($urls)) {
            abort(404);
        }

        return response()->view('sitemap.index', [
            'urls' => $urls,
        ])->header('Content-Type', 'application/xml');
    }

    /**
     * Generate static pages sitemap
     */
    public function static(): Response
    {
        return $this->byType('static');
    }

    /**
     * Generate movies sitemap
     */
    public function movies(): Response
    {
        return $this->byType('movies');
    }

    /**
     * Generate TV shows sitemap
     */
    public function tvShows(): Response
    {
        return $this->byType('tv-shows');
    }

    /**
     * Generate cast sitemap
     */
    public function cast(): Response
    {
        return $this->byType('cast');
    }

    /**
     * Generate episodes sitemap
     */
    public function episodes(): Response
    {
        return $this->byType('episodes');
    }
}

