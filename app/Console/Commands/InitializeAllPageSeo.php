<?php

namespace App\Console\Commands;

use App\Models\PageSeo;
use Illuminate\Console\Command;

class InitializeAllPageSeo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-seo:init-all {--force : Force update existing configurations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize default SEO configurations for all available pages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $availablePages = PageSeo::getAvailablePageKeys();
        $existingPages = PageSeo::pluck('page_key')->toArray();
        
        $this->info('🚀 Initializing SEO configurations for all pages...');
        $this->newLine();
        
        $created = 0;
        $updated = 0;
        $skipped = 0;
        
        foreach ($availablePages as $pageKey => $pageName) {
            $existing = PageSeo::where('page_key', $pageKey)->first();
            
            if ($existing) {
                if ($force) {
                    $this->updatePageSeo($existing, $pageKey, $pageName);
                    $updated++;
                    $this->line("✅ Updated: {$pageName} ({$pageKey})");
                } else {
                    $skipped++;
                    $this->line("⏭️  Skipped: {$pageName} ({$pageKey}) - already exists");
                }
            } else {
                $this->createPageSeo($pageKey, $pageName);
                $created++;
                $this->line("✨ Created: {$pageName} ({$pageKey})");
            }
        }
        
        $this->newLine();
        $this->info("📊 Summary:");
        $this->line("   Created: {$created}");
        $this->line("   Updated: {$updated}");
        $this->line("   Skipped: {$skipped}");
        $this->newLine();
        
        if ($created > 0 || $updated > 0) {
            $this->info('✅ All page SEO configurations have been processed!');
        } else {
            $this->info('ℹ️  All pages are already configured. Use --force to update existing configurations.');
        }
        
        return 0;
    }

    /**
     * Create page SEO configuration
     */
    protected function createPageSeo(string $pageKey, string $pageName)
    {
        $siteUrl = config('app.url', url('/'));
        $siteName = config('app.name', 'Nazaarabox');
        $defaults = $this->getDefaultsForPage($pageKey, $pageName, $siteUrl, $siteName);
        
        PageSeo::create($defaults);
    }

    /**
     * Update existing page SEO configuration
     */
    protected function updatePageSeo(PageSeo $pageSeo, string $pageKey, string $pageName)
    {
        $siteUrl = config('app.url', url('/'));
        $siteName = config('app.name', 'Nazaarabox');
        $defaults = $this->getDefaultsForPage($pageKey, $pageName, $siteUrl, $siteName);
        
        // Only update fields that are empty
        foreach ($defaults as $key => $value) {
            if ($key === 'page_key' || $key === 'is_active') {
                continue; // Skip these fields
            }
            
            if (empty($pageSeo->$key) && !empty($value)) {
                $pageSeo->$key = $value;
            }
        }
        
        $pageSeo->save();
    }

    /**
     * Get default SEO values for a specific page
     */
    protected function getDefaultsForPage(string $pageKey, string $pageName, string $siteUrl, string $siteName): array
    {
        $baseDefaults = [
            'page_key' => $pageKey,
            'page_name' => $pageName,
            'meta_robots' => 'index, follow',
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image',
            'canonical_url' => $siteUrl . $this->getPagePath($pageKey),
            'is_active' => true,
        ];

        $pageSpecificDefaults = [
            'home' => [
                'meta_title' => "{$siteName} - Watch Movies & TV Shows Online",
                'meta_description' => 'Discover and watch thousands of movies and TV shows online. Browse popular content, top-rated titles, and latest releases. Download and stream in high quality.',
                'meta_keywords' => 'movies, tv shows, streaming, watch online, download movies, entertainment, latest movies, popular tv shows',
                'og_title' => "{$siteName} - Watch Movies & TV Shows Online",
                'og_description' => 'Discover and watch thousands of movies and TV shows online. Browse popular content, top-rated titles, and latest releases.',
                'og_image' => asset('favicon.ico'),
                'og_url' => $siteUrl,
                'twitter_title' => "{$siteName} - Watch Movies & TV Shows Online",
                'twitter_description' => 'Discover and watch thousands of movies and TV shows online. Browse popular content, top-rated titles, and latest releases.',
                'twitter_image' => asset('favicon.ico'),
                'schema_markup' => json_encode([
                    '@context' => 'https://schema.org',
                    '@type' => 'WebSite',
                    'name' => $siteName,
                    'url' => $siteUrl,
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => [
                            '@type' => 'EntryPoint',
                            'urlTemplate' => $siteUrl . '/search?q={search_term_string}'
                        ],
                        'query-input' => 'required name=search_term_string'
                    ]
                ], JSON_PRETTY_PRINT),
            ],
            'movies.index' => [
                'meta_title' => "Movies - Browse All Movies | {$siteName}",
                'meta_description' => 'Browse our complete collection of movies. Find action, drama, comedy, thriller, horror movies and more. Watch and download movies in high quality.',
                'meta_keywords' => 'movies, watch movies, download movies, latest movies, popular movies, action movies, drama movies, comedy movies',
                'og_title' => "Movies - Browse All Movies | {$siteName}",
                'og_description' => 'Browse our complete collection of movies. Find action, drama, comedy, thriller, horror movies and more.',
                'og_image' => asset('favicon.ico'),
                'og_url' => $siteUrl . '/movies',
                'twitter_title' => "Movies - Browse All Movies | {$siteName}",
                'twitter_description' => 'Browse our complete collection of movies. Find action, drama, comedy, thriller, horror movies and more.',
                'twitter_image' => asset('favicon.ico'),
            ],
            'tv-shows.index' => [
                'meta_title' => "TV Shows - Browse All TV Series | {$siteName}",
                'meta_description' => 'Browse our complete collection of TV shows and series. Find drama, comedy, action, thriller series and more. Watch and download TV shows in high quality.',
                'meta_keywords' => 'tv shows, tv series, watch tv shows, download tv shows, drama series, comedy series, latest tv shows',
                'og_title' => "TV Shows - Browse All TV Series | {$siteName}",
                'og_description' => 'Browse our complete collection of TV shows and series. Find drama, comedy, action, thriller series and more.',
                'og_image' => asset('favicon.ico'),
                'og_url' => $siteUrl . '/tv-shows',
                'twitter_title' => "TV Shows - Browse All TV Series | {$siteName}",
                'twitter_description' => 'Browse our complete collection of TV shows and series. Find drama, comedy, action, thriller series and more.',
                'twitter_image' => asset('favicon.ico'),
            ],
            'cast.index' => [
                'meta_title' => "Cast & Actors - Browse All Actors | {$siteName}",
                'meta_description' => 'Browse our collection of actors and cast members. Discover popular actors, their movies and TV shows, biographies and more.',
                'meta_keywords' => 'actors, cast, celebrities, movie stars, tv actors, popular actors, actor profiles',
                'og_title' => "Cast & Actors - Browse All Actors | {$siteName}",
                'og_description' => 'Browse our collection of actors and cast members. Discover popular actors, their movies and TV shows.',
                'og_image' => asset('favicon.ico'),
                'og_url' => $siteUrl . '/cast',
                'twitter_title' => "Cast & Actors - Browse All Actors | {$siteName}",
                'twitter_description' => 'Browse our collection of actors and cast members. Discover popular actors, their movies and TV shows.',
                'twitter_image' => asset('favicon.ico'),
            ],
            'search' => [
                'meta_title' => "Search Movies & TV Shows | {$siteName}",
                'meta_description' => 'Search for your favorite movies and TV shows. Find exactly what you\'re looking for with our powerful search feature.',
                'meta_keywords' => 'search movies, search tv shows, find movies, movie search, tv show search',
                'og_title' => "Search Movies & TV Shows | {$siteName}",
                'og_description' => 'Search for your favorite movies and TV shows. Find exactly what you\'re looking for.',
                'og_image' => asset('favicon.ico'),
                'og_url' => $siteUrl . '/search',
                'twitter_title' => "Search Movies & TV Shows | {$siteName}",
                'twitter_description' => 'Search for your favorite movies and TV shows. Find exactly what you\'re looking for.',
                'twitter_image' => asset('favicon.ico'),
                'meta_robots' => 'noindex, follow',
            ],
            'about' => [
                'meta_title' => "About Us | {$siteName}",
                'meta_description' => "Learn more about {$siteName}. Discover our mission, values, and commitment to providing quality entertainment content.",
                'meta_keywords' => 'about us, company information, mission, values',
                'og_title' => "About Us | {$siteName}",
                'og_description' => "Learn more about {$siteName}. Discover our mission and values.",
                'og_image' => asset('favicon.ico'),
                'og_url' => $siteUrl . '/about',
                'twitter_title' => "About Us | {$siteName}",
                'twitter_description' => "Learn more about {$siteName}. Discover our mission and values.",
                'twitter_image' => asset('favicon.ico'),
            ],
            'dmca' => [
                'meta_title' => "DMCA Policy | {$siteName}",
                'meta_description' => "Read our DMCA policy and learn how to submit a copyright infringement notice. We respect intellectual property rights.",
                'meta_keywords' => 'dmca, copyright, policy, intellectual property, takedown notice',
                'og_title' => "DMCA Policy | {$siteName}",
                'og_description' => "Read our DMCA policy and learn how to submit a copyright infringement notice.",
                'og_image' => asset('favicon.ico'),
                'og_url' => $siteUrl . '/dmca',
                'twitter_title' => "DMCA Policy | {$siteName}",
                'twitter_description' => "Read our DMCA policy and learn how to submit a copyright infringement notice.",
                'twitter_image' => asset('favicon.ico'),
                'meta_robots' => 'noindex, follow',
            ],
            'completed' => [
                'meta_title' => "Completed TV Shows & Series | {$siteName}",
                'meta_description' => 'Browse our collection of completed TV shows and series. Find finished series with all episodes available.',
                'meta_keywords' => 'completed tv shows, finished series, completed series, all episodes available',
                'og_title' => "Completed TV Shows & Series | {$siteName}",
                'og_description' => 'Browse our collection of completed TV shows and series. Find finished series with all episodes available.',
                'og_image' => asset('favicon.ico'),
                'og_url' => $siteUrl . '/completed',
                'twitter_title' => "Completed TV Shows & Series | {$siteName}",
                'twitter_description' => 'Browse our collection of completed TV shows and series. Find finished series with all episodes available.',
                'twitter_image' => asset('favicon.ico'),
            ],
            'upcoming' => [
                'meta_title' => "Upcoming Movies & TV Shows | {$siteName}",
                'meta_description' => 'Discover upcoming movies and TV shows. Stay updated with the latest releases and upcoming content.',
                'meta_keywords' => 'upcoming movies, upcoming tv shows, latest releases, new releases, coming soon',
                'og_title' => "Upcoming Movies & TV Shows | {$siteName}",
                'og_description' => 'Discover upcoming movies and TV shows. Stay updated with the latest releases.',
                'og_image' => asset('favicon.ico'),
                'og_url' => $siteUrl . '/upcoming',
                'twitter_title' => "Upcoming Movies & TV Shows | {$siteName}",
                'twitter_description' => 'Discover upcoming movies and TV shows. Stay updated with the latest releases.',
                'twitter_image' => asset('favicon.ico'),
            ],
        ];

        return array_merge(
            $baseDefaults,
            $pageSpecificDefaults[$pageKey] ?? []
        );
    }

    /**
     * Get the URL path for a page key
     */
    protected function getPagePath(string $pageKey): string
    {
        $paths = [
            'home' => '/',
            'movies.index' => '/movies',
            'tv-shows.index' => '/tv-shows',
            'cast.index' => '/cast',
            'search' => '/search',
            'about' => '/about',
            'dmca' => '/dmca',
            'completed' => '/completed',
            'upcoming' => '/upcoming',
        ];

        return $paths[$pageKey] ?? '/';
    }
}

