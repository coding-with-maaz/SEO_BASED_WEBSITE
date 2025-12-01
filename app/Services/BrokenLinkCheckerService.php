<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BrokenLinkCheckerService
{
    protected $siteUrl;
    protected $timeout;
    protected $maxRedirects;

    public function __construct()
    {
        $this->siteUrl = rtrim(config('app.url', url('/')), '/');
        $this->timeout = 10;
        $this->maxRedirects = 5;
    }

    /**
     * Check a single URL
     */
    public function checkUrl(string $url, bool $followRedirects = true): array
    {
        // Normalize URL
        $url = $this->normalizeUrl($url);
        
        // For localhost URLs, try internal request first
        if ($this->isLocalhost($url) && !app()->runningInConsole()) {
            try {
                return $this->checkLocalhostUrl($url);
            } catch (\Exception $e) {
                // Fall through to HTTP request
            }
        }
        
        try {
            // Increase timeout for localhost URLs
            $timeout = $this->isLocalhost($url) ? 30 : $this->timeout;
            
            $response = Http::timeout($timeout)
                ->withOptions([
                    'allow_redirects' => [
                        'max' => $followRedirects ? $this->maxRedirects : 0,
                        'strict' => false,
                        'referer' => true,
                    ],
                ])
                ->get($url);
            
            $statusCode = $response->status();
            $isBroken = $statusCode >= 400;
            
            return [
                'url' => $url,
                'status_code' => $statusCode,
                'is_broken' => $isBroken,
                'is_redirect' => $statusCode >= 300 && $statusCode < 400,
                'final_url' => $response->effectiveUri() ?? $url,
                'message' => $this->getStatusMessage($statusCode),
                'checked_at' => now()->toIso8601String(),
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return [
                'url' => $url,
                'status_code' => 0,
                'is_broken' => true,
                'is_redirect' => false,
                'final_url' => $url,
                'message' => 'Connection timeout or failed',
                'error' => $e->getMessage(),
                'checked_at' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            return [
                'url' => $url,
                'status_code' => 0,
                'is_broken' => true,
                'is_redirect' => false,
                'final_url' => $url,
                'message' => 'Error checking URL: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'checked_at' => now()->toIso8601String(),
            ];
        }
    }

    /**
     * Check multiple URLs
     */
    public function checkUrls(array $urls, bool $followRedirects = true): array
    {
        $results = [];
        
        foreach ($urls as $url) {
            $results[] = $this->checkUrl($url, $followRedirects);
        }
        
        return $results;
    }

    /**
     * Check all links from a page
     */
    public function checkPageLinks(string $pageUrl): array
    {
        try {
            // Increase timeout for localhost URLs
            $timeout = (str_contains($pageUrl, '127.0.0.1') || str_contains($pageUrl, 'localhost')) ? 30 : $this->timeout;
            $response = Http::timeout($timeout)->get($pageUrl);
            
            if (!$response->successful()) {
                return [
                    'page_url' => $pageUrl,
                    'success' => false,
                    'error' => "Failed to fetch page: HTTP {$response->status()}",
                    'links' => [],
                ];
            }

            $html = $response->body();
            $links = $this->extractLinks($html, $pageUrl);
            
            $results = [
                'page_url' => $pageUrl,
                'success' => true,
                'total_links' => count($links),
                'links' => [],
            ];

            foreach ($links as $link) {
                $results['links'][] = $this->checkUrl($link);
            }

            return $results;
        } catch (\Exception $e) {
            return [
                'page_url' => $pageUrl,
                'success' => false,
                'error' => $e->getMessage(),
                'links' => [],
            ];
        }
    }

    /**
     * Extract all links from HTML
     */
    protected function extractLinks(string $html, string $baseUrl): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);
        
        $links = [];
        $linkNodes = $xpath->query('//a[@href]');
        
        foreach ($linkNodes as $node) {
            $href = $node->getAttribute('href');
            $absoluteUrl = $this->makeAbsoluteUrl($href, $baseUrl);
            
            if ($absoluteUrl && !in_array($absoluteUrl, $links)) {
                $links[] = $absoluteUrl;
            }
        }
        
        return $links;
    }

    /**
     * Convert relative URL to absolute
     */
    protected function makeAbsoluteUrl(string $url, string $baseUrl): ?string
    {
        // Skip anchors, javascript, mailto, tel
        if (preg_match('/^(#|javascript:|mailto:|tel:)/i', $url)) {
            return null;
        }
        
        // Already absolute
        if (preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }
        
        // Parse base URL
        $base = parse_url($baseUrl);
        if (!$base) {
            return null;
        }
        
        // Relative URL
        if (Str::startsWith($url, '/')) {
            return $base['scheme'] . '://' . $base['host'] . $url;
        }
        
        // Relative path
        $path = isset($base['path']) ? dirname($base['path']) : '/';
        if ($path !== '/') {
            $path = rtrim($path, '/') . '/';
        }
        
        return $base['scheme'] . '://' . $base['host'] . $path . $url;
    }

    /**
     * Normalize URL
     */
    protected function normalizeUrl(string $url): string
    {
        // Remove fragment
        $url = preg_replace('/#.*$/', '', $url);
        
        // Ensure protocol
        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://' . ltrim($url, '/');
        }
        
        return rtrim($url, '/');
    }

    /**
     * Get status message
     */
    protected function getStatusMessage(int $statusCode): string
    {
        $messages = [
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            204 => 'No Content',
            301 => 'Moved Permanently',
            302 => 'Found (Temporary Redirect)',
            303 => 'See Other',
            304 => 'Not Modified',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            410 => 'Gone',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
        ];
        
        return $messages[$statusCode] ?? "HTTP {$statusCode}";
    }

    /**
     * Check sitemap URLs (with batching to prevent timeouts)
     */
    public function checkSitemapUrls(int $limit = 50): array
    {
        $sitemapService = app(\App\Services\SitemapService::class);
        $allUrls = $sitemapService->getAllUrlsFlat();
        
        $urls = array_column($allUrls, 'loc');
        
        // Limit the number of URLs to check to prevent timeouts
        if ($limit > 0 && count($urls) > $limit) {
            $urls = array_slice($urls, 0, $limit);
        }
        
        return $this->checkUrls($urls);
    }

    /**
     * Check localhost URL using internal request
     */
    protected function checkLocalhostUrl(string $url): array
    {
        try {
            $parsedUrl = parse_url($url);
            $path = $parsedUrl['path'] ?? '/';
            
            // Create internal request
            $request = \Illuminate\Http\Request::create($path, 'GET');
            $request->headers->set('Host', $parsedUrl['host'] ?? 'localhost');
            $request->headers->set('X-Internal-Request', 'true');
            
            // Store original request
            $originalRequest = request();
            
            // Handle request
            $response = app()->handle($request);
            
            // Restore original request
            app()->instance('request', $originalRequest);
            
            $statusCode = $response->getStatusCode();
            $isBroken = $statusCode >= 400;
            
            return [
                'url' => $url,
                'status_code' => $statusCode,
                'is_broken' => $isBroken,
                'is_redirect' => $statusCode >= 300 && $statusCode < 400,
                'final_url' => $url,
                'message' => $this->getStatusMessage($statusCode),
                'checked_at' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            throw $e; // Re-throw to fall back to HTTP
        }
    }

    /**
     * Check if URL is localhost
     */
    protected function isLocalhost(string $url): bool
    {
        return str_contains($url, '127.0.0.1') 
            || str_contains($url, 'localhost') 
            || str_contains($url, '::1');
    }

    /**
     * Get broken links summary
     */
    public function getBrokenLinksSummary(array $results): array
    {
        $broken = array_filter($results, fn($r) => $r['is_broken'] ?? false);
        $redirects = array_filter($results, fn($r) => $r['is_redirect'] ?? false);
        
        return [
            'total' => count($results),
            'broken' => count($broken),
            'redirects' => count($redirects),
            'working' => count($results) - count($broken),
            'broken_links' => array_values($broken),
            'redirect_links' => array_values($redirects),
        ];
    }
}

