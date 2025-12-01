<?php

namespace App\Console\Commands;

use App\Services\SitemapSubmissionService;
use Illuminate\Console\Command;

class SubmitSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:submit-sitemap 
                            {--engine= : Submit to specific engine (google, bing, yandex)}
                            {--all : Submit to all enabled engines}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Submit sitemap to search engines';

    /**
     * Execute the console command.
     */
    public function handle(SitemapSubmissionService $service): int
    {
        $this->info('Submitting sitemap to search engines...');
        
        $engine = $this->option('engine');
        $all = $this->option('all') || !$engine;
        
        if ($all) {
            $results = $service->submitToAll();
        } else {
            $results = [$engine => $service->submitTo($engine)];
        }
        
        $this->displayResults($results);
        
        return Command::SUCCESS;
    }

    /**
     * Display submission results
     */
    protected function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('Submission Results:');
        $this->newLine();
        
        foreach ($results as $engine => $result) {
            if ($result['success'] ?? false) {
                $this->line("  ✓ {$engine}: {$result['message']}");
            } elseif ($result['skipped'] ?? false) {
                $this->line("  ⊘ {$engine}: {$result['message']} (skipped)");
            } else {
                $this->error("  ✗ {$engine}: {$result['message']}");
            }
        }
        
        $this->newLine();
    }
}

