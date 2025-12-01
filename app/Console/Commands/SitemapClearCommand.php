<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;

class SitemapClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the sitemap cache';

    /**
     * Execute the console command.
     */
    public function handle(SitemapService $sitemapService): int
    {
        $sitemapService->clearCache();
        
        $this->info('Sitemap cache cleared successfully!');
        
        return Command::SUCCESS;
    }
}

