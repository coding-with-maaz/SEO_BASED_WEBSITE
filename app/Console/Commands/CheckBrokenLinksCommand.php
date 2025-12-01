<?php

namespace App\Console\Commands;

use App\Services\BrokenLinkCheckerService;
use Illuminate\Console\Command;

class CheckBrokenLinksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:check-links 
                            {--url= : Single URL to check}
                            {--page= : Check all links on a page}
                            {--sitemap : Check all URLs from sitemap}
                            {--format=table : Output format (table, json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for broken links';

    /**
     * Execute the console command.
     */
    public function handle(BrokenLinkCheckerService $service): int
    {
        $url = $this->option('url');
        $page = $this->option('page');
        $sitemap = $this->option('sitemap');
        $format = $this->option('format');
        
        if ($sitemap) {
            $this->info('Checking all URLs from sitemap...');
            $results = $service->checkSitemapUrls();
        } elseif ($page) {
            $this->info("Checking all links on page: {$page}");
            $pageResult = $service->checkPageLinks($page);
            $results = $pageResult['links'] ?? [];
        } elseif ($url) {
            $this->info("Checking URL: {$url}");
            $results = [$service->checkUrl($url)];
        } else {
            $this->error('Please specify --url, --page, or --sitemap');
            return Command::FAILURE;
        }
        
        if ($format === 'json') {
            $this->line(json_encode($results, JSON_PRETTY_PRINT));
            return Command::SUCCESS;
        }
        
        $this->displayResults($results, $service);
        
        return Command::SUCCESS;
    }

    /**
     * Display broken links results
     */
    protected function displayResults(array $results, BrokenLinkCheckerService $service): void
    {
        $summary = $service->getBrokenLinksSummary($results);
        
        $this->newLine();
        $this->info('Link Check Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Total Links', $summary['total']],
                ['Working', $summary['working']],
                ['Broken', $summary['broken']],
                ['Redirects', $summary['redirects']],
            ]
        );
        
        if ($summary['broken'] > 0) {
            $this->newLine();
            $this->error('Broken Links:');
            $this->table(
                ['URL', 'Status', 'Message'],
                array_map(function($link) {
                    return [
                        $link['url'],
                        $link['status_code'] ?? 'N/A',
                        $link['message'] ?? 'Unknown error',
                    ];
                }, $summary['broken_links'])
            );
        }
        
        if ($summary['redirects'] > 0) {
            $this->newLine();
            $this->warn('Redirects:');
            $this->table(
                ['URL', 'Status', 'Final URL'],
                array_map(function($link) {
                    return [
                        $link['url'],
                        $link['status_code'] ?? 'N/A',
                        $link['final_url'] ?? $link['url'],
                    ];
                }, array_slice($summary['redirect_links'], 0, 10))
            );
        }
    }
}

