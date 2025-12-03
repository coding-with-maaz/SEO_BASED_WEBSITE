<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Generate enhanced robots.txt dynamically
     */
    public function index(): Response
    {
        $siteUrl = rtrim(config('app.url'), '/');
        
        $lines = [];
        
        // Header comment
        $lines[] = "# robots.txt for " . config('app.name', 'Nazaarabox');
        $lines[] = "# Generated dynamically - " . now()->format('Y-m-d H:i:s');
        $lines[] = "";
        
        // Googlebot specific rules
        $lines[] = "# Googlebot rules";
        $lines[] = "User-agent: Googlebot";
        $lines[] = "Allow: /";
        $lines[] = "Disallow: /admin";
        $lines[] = "Disallow: /admin/*";
        $lines[] = "Crawl-delay: 1";
        $lines[] = "";
        
        // Bingbot specific rules
        $lines[] = "# Bingbot rules";
        $lines[] = "User-agent: Bingbot";
        $lines[] = "Allow: /";
        $lines[] = "Disallow: /admin";
        $lines[] = "Disallow: /admin/*";
        $lines[] = "Crawl-delay: 1";
        $lines[] = "";
        
        // General rules for all bots
        $lines[] = "# Rules for all search engines";
        $lines[] = "User-agent: *";
        $lines[] = "";
        $lines[] = "# Allow public content";
        $lines[] = "Allow: /";
        $lines[] = "Allow: /movies";
        $lines[] = "Allow: /movies/*";
        $lines[] = "Allow: /tv-shows";
        $lines[] = "Allow: /tv-shows/*";
        $lines[] = "Allow: /cast";
        $lines[] = "Allow: /cast/*";
        $lines[] = "Allow: /about";
        $lines[] = "Allow: /dmca";
        $lines[] = "Allow: /completed";
        $lines[] = "Allow: /upcoming";
        $lines[] = "Allow: /how-to-download";
        $lines[] = "";
        $lines[] = "# Block admin and system areas";
        $lines[] = "Disallow: /admin";
        $lines[] = "Disallow: /admin/*";
        $lines[] = "";
        $lines[] = "# Block Laravel system files and directories";
        $lines[] = "Disallow: /vendor/";
        $lines[] = "Disallow: /storage/";
        $lines[] = "Disallow: /bootstrap/";
        $lines[] = "Disallow: /config/";
        $lines[] = "Disallow: /database/";
        $lines[] = "Disallow: /resources/";
        $lines[] = "Disallow: /routes/";
        $lines[] = "Disallow: /tests/";
        $lines[] = "Disallow: /.env";
        $lines[] = "Disallow: /.git/";
        $lines[] = "Disallow: /.gitignore";
        $lines[] = "Disallow: /composer.json";
        $lines[] = "Disallow: /composer.lock";
        $lines[] = "Disallow: /package.json";
        $lines[] = "Disallow: /artisan";
        $lines[] = "";
        $lines[] = "# Block API and system JSON files";
        $lines[] = "Disallow: /api/";
        $lines[] = "Disallow: /*.json$";
        $lines[] = "";
        $lines[] = "# Allow important files (sitemaps, robots.txt)";
        $lines[] = "Allow: /sitemap.xml";
        $lines[] = "Allow: /sitemap/";
        $lines[] = "Allow: /sitemap/*.xml";
        $lines[] = "Allow: /robots.txt";
        $lines[] = "";
        $lines[] = "# Crawl delay for all bots";
        $lines[] = "Crawl-delay: 1";
        $lines[] = "";
        
        // Host directive (helps with canonical URLs)
        $host = parse_url($siteUrl, PHP_URL_HOST);
        if ($host) {
            $lines[] = "# Preferred host";
            $lines[] = "Host: {$host}";
            $lines[] = "";
        }
        
        // Control aggressive crawlers
        $lines[] = "# Control aggressive crawlers (rate limiting)";
        $lines[] = "User-agent: AhrefsBot";
        $lines[] = "Crawl-delay: 5";
        $lines[] = "";
        $lines[] = "User-agent: SemrushBot";
        $lines[] = "Crawl-delay: 5";
        $lines[] = "";
        $lines[] = "# Block unwanted bots";
        $lines[] = "User-agent: MJ12bot";
        $lines[] = "Disallow: /";
        $lines[] = "";
        $lines[] = "User-agent: DotBot";
        $lines[] = "Disallow: /";
        $lines[] = "";
        $lines[] = "User-agent: BlexBot";
        $lines[] = "Disallow: /";
        $lines[] = "";
        $lines[] = "User-agent: MegaIndex";
        $lines[] = "Disallow: /";
        $lines[] = "";
        
        // Sitemaps
        $lines[] = "# Sitemaps";
        $lines[] = "Sitemap: {$siteUrl}/sitemap.xml";
        $lines[] = "Sitemap: {$siteUrl}/sitemap/index.xml";
        $lines[] = "Sitemap: {$siteUrl}/sitemap/static.xml";
        $lines[] = "Sitemap: {$siteUrl}/sitemap/movies.xml";
        $lines[] = "Sitemap: {$siteUrl}/sitemap/tv-shows.xml";
        $lines[] = "Sitemap: {$siteUrl}/sitemap/cast.xml";
        $lines[] = "Sitemap: {$siteUrl}/sitemap/episodes.xml";
        
        $content = implode("\n", $lines);
        
        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}

