<?php

namespace App\Console\Commands;

use App\Services\RichSnippetsTestService;
use Illuminate\Console\Command;

class TestRichSnippetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:test-rich-snippets 
                            {url : URL to test}
                            {--format=table : Output format (table, json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test rich snippets / structured data for a URL';

    /**
     * Execute the console command.
     */
    public function handle(RichSnippetsTestService $service): int
    {
        $url = $this->argument('url');
        $format = $this->option('format');
        
        $this->info("Testing rich snippets for: {$url}");
        $this->newLine();
        
        $result = $service->testWithGoogle($url);
        
        if (!($result['success'] ?? false)) {
            $error = $result['error'] ?? 'Unknown error';
            $this->error("Failed to test URL: {$error}");
            return Command::FAILURE;
        }
        
        if ($format === 'json') {
            $this->line(json_encode($result, JSON_PRETTY_PRINT));
            return Command::SUCCESS;
        }
        
        $this->displayResults($result, $service);
        
        return Command::SUCCESS;
    }

    /**
     * Display rich snippets test results
     */
    protected function displayResults(array $result, RichSnippetsTestService $service): void
    {
        $this->info('Rich Snippets Test Results:');
        $this->newLine();
        
        // Status
        $status = ($result['valid'] ?? false) ? '✓ Valid' : '✗ Invalid';
        $this->line("Status: {$status}");
        $this->newLine();
        
        // Formats found
        if (!empty($result['formats_found'] ?? [])) {
            $this->info('Structured Data Formats Found:');
            foreach ($result['formats_found'] as $format) {
                $this->line("  • {$format}");
            }
            $this->newLine();
        }
        
        // Schemas
        if (!empty($result['schemas'] ?? [])) {
            $this->info('Schemas Detected:');
            foreach ($result['schemas'] as $schema) {
                $schemaStatus = ($schema['valid'] ?? false) ? '✓' : '✗';
                $this->line("  {$schemaStatus} {$schema['type']} ({$schema['format']})");
                
                if (!empty($schema['errors'] ?? [])) {
                    foreach ($schema['errors'] as $error) {
                        $this->error("    Error: {$error}");
                    }
                }
                if (!empty($schema['warnings'] ?? [])) {
                    foreach ($schema['warnings'] as $warning) {
                        $this->warn("    Warning: {$warning}");
                    }
                }
            }
            $this->newLine();
        }
        
        // Errors
        if (!empty($result['errors'] ?? [])) {
            $this->error('Errors:');
            foreach ($result['errors'] as $error) {
                $this->line("  • {$error}");
            }
            $this->newLine();
        }
        
        // Warnings
        if (!empty($result['warnings'] ?? [])) {
            $this->warn('Warnings:');
            foreach ($result['warnings'] as $warning) {
                $this->line("  • {$warning}");
            }
            $this->newLine();
        }
        
        // Manual test URLs
        $this->info('Manual Testing URLs:');
        $this->line("  Google Rich Results: {$service->getGoogleTestUrl($result['url'])}");
        $this->line("  Schema Validator: {$service->getSchemaValidatorUrl($result['url'])}");
    }
}

