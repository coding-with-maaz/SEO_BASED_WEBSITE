<?php

namespace App\Console\Commands;

use App\Services\SeoAnalyzerService;
use Illuminate\Console\Command;

class CheckSeoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:check 
                            {url : URL to check}
                            {--format=table : Output format (table, json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SEO score for a URL';

    /**
     * Execute the console command.
     */
    public function handle(SeoAnalyzerService $service): int
    {
        $url = $this->argument('url');
        $format = $this->option('format');
        
        $this->info("Analyzing SEO for: {$url}");
        $this->newLine();
        
        $analysis = $service->analyzeUrl($url);
        
        if (!($analysis['success'] ?? false)) {
            $error = $analysis['error'] ?? 'Unknown error';
            $this->error("Failed to analyze URL: {$error}");
            return Command::FAILURE;
        }
        
        if ($format === 'json') {
            $this->line(json_encode($analysis, JSON_PRETTY_PRINT));
            return Command::SUCCESS;
        }
        
        $this->displayAnalysis($analysis);
        
        return Command::SUCCESS;
    }

    /**
     * Display SEO analysis
     */
    protected function displayAnalysis(array $analysis): void
    {
        $score = $analysis['score'] ?? 0;
        $maxScore = $analysis['max_score'] ?? 100;
        
        // Score bar
        $this->info("SEO Score: {$score}/{$maxScore}");
        $bar = str_repeat('█', round($score / 2));
        $this->line($bar);
        $this->newLine();
        
        // Checks
        $this->info('Checks:');
        $this->table(
            ['Check', 'Status', 'Score', 'Issues'],
            $this->formatChecks($analysis['checks'] ?? [])
        );
        
        // Recommendations
        if (!empty($analysis['recommendations'] ?? [])) {
            $this->newLine();
            $this->warn('Recommendations:');
            foreach ($analysis['recommendations'] as $rec) {
                $this->line("  • [{$rec['check']}] {$rec['issue']}");
            }
        }
    }

    /**
     * Format checks for table
     */
    protected function formatChecks(array $checks): array
    {
        $rows = [];
        
        foreach ($checks as $checkName => $check) {
            $status = ($check['passed'] ?? false) ? '✓ Pass' : '✗ Fail';
            $score = ($check['score'] ?? 0) . '/' . ($check['max_score'] ?? 0);
            $issues = count($check['issues'] ?? []);
            
            $rows[] = [
                ucfirst(str_replace('_', ' ', $checkName)),
                $status,
                $score,
                $issues > 0 ? "{$issues} issue(s)" : 'None',
            ];
        }
        
        return $rows;
    }
}

