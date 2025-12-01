<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SitemapSubmissionService
{
    protected $sitemapUrl;
    protected $searchEngines;

    public function __construct()
    {
        $this->sitemapUrl = route('sitemap.index');
        $this->searchEngines = [
            'google' => [
                'name' => 'Google',
                'ping_url' => 'https://www.google.com/ping?sitemap=',
                'enabled' => env('SEO_SUBMIT_TO_GOOGLE', true),
            ],
            'bing' => [
                'name' => 'Bing',
                'ping_url' => 'https://www.bing.com/ping?sitemap=',
                'enabled' => env('SEO_SUBMIT_TO_BING', true),
            ],
            'yandex' => [
                'name' => 'Yandex',
                'ping_url' => 'https://webmaster.yandex.com/ping?sitemap=',
                'enabled' => env('SEO_SUBMIT_TO_YANDEX', false),
            ],
        ];
    }

    /**
     * Submit sitemap to all enabled search engines
     */
    public function submitToAll(): array
    {
        $results = [];
        
        foreach ($this->searchEngines as $engine => $config) {
            if (!$config['enabled']) {
                $results[$engine] = [
                    'success' => false,
                    'message' => 'Disabled',
                    'skipped' => true,
                ];
                continue;
            }

            $results[$engine] = $this->submitTo($engine);
        }

        return $results;
    }

    /**
     * Submit sitemap to a specific search engine
     */
    public function submitTo(string $engine): array
    {
        if (!isset($this->searchEngines[$engine])) {
            return [
                'success' => false,
                'message' => "Unknown search engine: {$engine}",
            ];
        }

        $config = $this->searchEngines[$engine];
        
        if (!$config['enabled']) {
            return [
                'success' => false,
                'message' => 'Engine is disabled',
                'skipped' => true,
            ];
        }

        $pingUrl = $config['ping_url'] . urlencode($this->sitemapUrl);
        
        try {
            $response = Http::timeout(10)->get($pingUrl);
            
            $success = $response->successful();
            
            $result = [
                'success' => $success,
                'message' => $success 
                    ? "Successfully submitted to {$config['name']}" 
                    : "Failed to submit to {$config['name']}: HTTP {$response->status()}",
                'status_code' => $response->status(),
                'engine' => $config['name'],
                'sitemap_url' => $this->sitemapUrl,
                'submitted_at' => now()->toIso8601String(),
            ];

            if ($success) {
                Log::info("Sitemap submitted to {$config['name']}", $result);
            } else {
                Log::warning("Failed to submit sitemap to {$config['name']}", $result);
            }

            return $result;
        } catch (\Exception $e) {
            $result = [
                'success' => false,
                'message' => "Error submitting to {$config['name']}: " . $e->getMessage(),
                'engine' => $config['name'],
                'error' => $e->getMessage(),
                'submitted_at' => now()->toIso8601String(),
            ];

            Log::error("Exception while submitting sitemap to {$config['name']}", $result);
            
            return $result;
        }
    }

    /**
     * Submit sitemap to Google Search Console (requires API key)
     */
    public function submitToGoogleSearchConsole(): array
    {
        // This would require Google Search Console API integration
        // For now, we'll use the ping method
        return $this->submitTo('google');
    }

    /**
     * Get submission history from cache
     */
    public function getSubmissionHistory(): array
    {
        return Cache::get('sitemap_submission_history', []);
    }

    /**
     * Save submission result to history
     */
    protected function saveToHistory(array $results): void
    {
        $history = $this->getSubmissionHistory();
        
        $history[] = [
            'timestamp' => now()->toIso8601String(),
            'results' => $results,
        ];

        // Keep only last 50 submissions
        $history = array_slice($history, -50);
        
        Cache::put('sitemap_submission_history', $history, now()->addDays(30));
    }

    /**
     * Submit and save to history
     */
    public function submitAndSave(): array
    {
        $results = $this->submitToAll();
        $this->saveToHistory($results);
        return $results;
    }

    /**
     * Get last submission status
     */
    public function getLastSubmissionStatus(): ?array
    {
        $history = $this->getSubmissionHistory();
        return !empty($history) ? end($history) : null;
    }
}

