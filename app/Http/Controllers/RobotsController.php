<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Generate robots.txt dynamically
     */
    public function index(): Response
    {
        $siteUrl = config('app.url');
        
        $content = "User-agent: *\n";
        $content .= "Allow: /\n\n";
        $content .= "# Disallow admin area\n";
        $content .= "Disallow: /admin\n\n";
        $content .= "# Sitemaps\n";
        $content .= "Sitemap: {$siteUrl}/sitemap.xml\n";
        $content .= "Sitemap: {$siteUrl}/sitemap/index.xml\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}

