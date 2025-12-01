<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RichSnippetsTestService
{
    protected $googleRichResultsUrl = 'https://search.google.com/test/rich-results';
    protected $schemaOrgValidatorUrl = 'https://validator.schema.org/validate';

    /**
     * Test structured data using Google Rich Results Test
     */
    public function testWithGoogle(string $url): array
    {
        $cacheKey = 'rich_snippets_google_' . md5($url);
        
        return Cache::remember($cacheKey, 3600, function () use ($url) {
            try {
                // For localhost URLs, use internal request
                if ($this->isLocalhost($url)) {
                    return $this->testLocalhostUrl($url);
                }
                
                // For external URLs, use HTTP request
                $response = Http::timeout(15)->get($url);
                
                if (!$response->successful()) {
                    return [
                        'success' => false,
                        'error' => "Failed to fetch URL: HTTP {$response->status()}",
                        'url' => $url,
                    ];
                }

                $html = $response->body();
                return $this->validateStructuredData($html, $url);
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'url' => $url,
                ];
            }
        });
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
     * Test localhost URL using internal request
     */
    protected function testLocalhostUrl(string $url): array
    {
        try {
            // Extract path from URL
            $parsedUrl = parse_url($url);
            $path = $parsedUrl['path'] ?? '/';
            
            // Prevent recursion
            if (app()->runningInConsole() || request()->header('X-Internal-Request')) {
                // Fallback to HTTP with longer timeout
                return $this->testWithHttp($url, 60);
            }
            
            // Create a request to the application
            $request = \Illuminate\Http\Request::create($path, 'GET');
            $request->headers->set('Host', $parsedUrl['host'] ?? 'localhost');
            $request->headers->set('X-Internal-Request', 'true');
            
            // Store original request
            $originalRequest = request();
            
            // Handle the request and get response
            $response = app()->handle($request);
            
            // Restore original request
            app()->instance('request', $originalRequest);
            
            if ($response->getStatusCode() !== 200) {
                return [
                    'success' => false,
                    'error' => "Failed to fetch URL: HTTP {$response->getStatusCode()}",
                    'url' => $url,
                ];
            }
            
            $html = $response->getContent();
            return $this->validateStructuredData($html, $url);
        } catch (\Exception $e) {
            // Fallback to HTTP request
            return $this->testWithHttp($url, 60);
        }
    }

    /**
     * Test URL using HTTP request
     */
    protected function testWithHttp(string $url, int $timeout = 15): array
    {
        try {
            $response = Http::timeout($timeout)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => "Failed to fetch URL: HTTP {$response->status()}",
                    'url' => $url,
                ];
            }
            
            $html = $response->body();
            return $this->validateStructuredData($html, $url);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'url' => $url,
            ];
        }
    }

    /**
     * Validate structured data in HTML
     */
    protected function validateStructuredData(string $html, string $url): array
    {
        $result = [
            'url' => $url,
            'success' => true,
            'formats_found' => [],
            'schemas' => [],
            'errors' => [],
            'warnings' => [],
            'valid' => true,
        ];

        // Check for JSON-LD
        $jsonLdSchemas = $this->extractJsonLd($html);
        if (!empty($jsonLdSchemas)) {
            $result['formats_found'][] = 'JSON-LD';
            foreach ($jsonLdSchemas as $schema) {
                $validation = $this->validateJsonLdSchema($schema);
                $result['schemas'][] = $validation;
                
                if (!$validation['valid']) {
                    $result['valid'] = false;
                    $result['errors'] = array_merge($result['errors'], $validation['errors'] ?? []);
                }
                if (!empty($validation['warnings'])) {
                    $result['warnings'] = array_merge($result['warnings'], $validation['warnings']);
                }
            }
        }

        // Check for Microdata
        $microdataSchemas = $this->extractMicrodata($html);
        if (!empty($microdataSchemas)) {
            $result['formats_found'][] = 'Microdata';
            foreach ($microdataSchemas as $schema) {
                $validation = $this->validateMicrodataSchema($schema);
                $result['schemas'][] = $validation;
                
                if (!$validation['valid']) {
                    $result['valid'] = false;
                    $result['errors'] = array_merge($result['errors'], $validation['errors'] ?? []);
                }
            }
        }

        // Check for RDFa
        $rdfaSchemas = $this->extractRdfa($html);
        if (!empty($rdfaSchemas)) {
            $result['formats_found'][] = 'RDFa';
        }

        if (empty($result['formats_found'])) {
            $result['valid'] = false;
            $result['errors'][] = 'No structured data found on page';
        }

        return $result;
    }

    /**
     * Extract JSON-LD schemas from HTML
     */
    protected function extractJsonLd(string $html): array
    {
        $schemas = [];
        $pattern = '/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is';
        
        if (preg_match_all($pattern, $html, $matches)) {
            foreach ($matches[1] as $json) {
                $decoded = json_decode($json, true);
                if ($decoded && is_array($decoded)) {
                    $schemas[] = $decoded;
                }
            }
        }
        
        return $schemas;
    }

    /**
     * Extract Microdata schemas from HTML
     */
    protected function extractMicrodata(string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);
        
        $schemas = [];
        $items = $xpath->query('//*[@itemscope]');
        
        foreach ($items as $item) {
            $schema = $this->extractMicrodataItem($item, $xpath);
            if (!empty($schema)) {
                $schemas[] = $schema;
            }
        }
        
        return $schemas;
    }

    /**
     * Extract RDFa schemas from HTML
     */
    protected function extractRdfa(string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);
        
        $schemas = [];
        $items = $xpath->query('//*[@typeof]');
        
        return $schemas; // RDFa extraction is more complex, simplified for now
    }

    /**
     * Extract microdata item
     */
    protected function extractMicrodataItem($item, $xpath): array
    {
        $schema = [];
        
        $itemType = $item->getAttribute('itemtype');
        if ($itemType) {
            $schema['@type'] = $itemType;
        }
        
        $properties = $xpath->query('.//*[@itemprop]', $item);
        foreach ($properties as $prop) {
            $propName = $prop->getAttribute('itemprop');
            $propValue = trim($prop->textContent);
            
            if (!empty($propValue)) {
                if (isset($schema[$propName])) {
                    if (!is_array($schema[$propName])) {
                        $schema[$propName] = [$schema[$propName]];
                    }
                    $schema[$propName][] = $propValue;
                } else {
                    $schema[$propName] = $propValue;
                }
            }
        }
        
        return $schema;
    }

    /**
     * Validate JSON-LD schema
     */
    protected function validateJsonLdSchema(array $schema): array
    {
        $result = [
            'type' => $schema['@type'] ?? 'Unknown',
            'format' => 'JSON-LD',
            'valid' => true,
            'errors' => [],
            'warnings' => [],
        ];

        // Check for required @type
        if (!isset($schema['@type'])) {
            $result['valid'] = false;
            $result['errors'][] = 'Missing @type property';
        }

        // Validate common schema types
        $type = $schema['@type'] ?? '';
        
        switch ($type) {
            case 'Movie':
                $result = $this->validateMovieSchema($schema, $result);
                break;
            case 'TVSeries':
            case 'TVShow':
                $result = $this->validateTvSeriesSchema($schema, $result);
                break;
            case 'Person':
                $result = $this->validatePersonSchema($schema, $result);
                break;
            case 'WebSite':
                $result = $this->validateWebSiteSchema($schema, $result);
                break;
            case 'Organization':
                $result = $this->validateOrganizationSchema($schema, $result);
                break;
        }

        return $result;
    }

    /**
     * Validate Movie schema
     */
    protected function validateMovieSchema(array $schema, array $result): array
    {
        $required = ['name', 'image'];
        $recommended = ['description', 'datePublished', 'aggregateRating', 'director', 'actor'];
        
        foreach ($required as $field) {
            if (!isset($schema[$field])) {
                $result['valid'] = false;
                $result['errors'][] = "Missing required field: {$field}";
            }
        }
        
        foreach ($recommended as $field) {
            if (!isset($schema[$field])) {
                $result['warnings'][] = "Missing recommended field: {$field}";
            }
        }
        
        return $result;
    }

    /**
     * Validate TV Series schema
     */
    protected function validateTvSeriesSchema(array $schema, array $result): array
    {
        $required = ['name', 'image'];
        $recommended = ['description', 'startDate', 'actor', 'numberOfSeasons', 'numberOfEpisodes'];
        
        foreach ($required as $field) {
            if (!isset($schema[$field])) {
                $result['valid'] = false;
                $result['errors'][] = "Missing required field: {$field}";
            }
        }
        
        foreach ($recommended as $field) {
            if (!isset($schema[$field])) {
                $result['warnings'][] = "Missing recommended field: {$field}";
            }
        }
        
        return $result;
    }

    /**
     * Validate Person schema
     */
    protected function validatePersonSchema(array $schema, array $result): array
    {
        $required = ['name'];
        $recommended = ['image', 'description'];
        
        foreach ($required as $field) {
            if (!isset($schema[$field])) {
                $result['valid'] = false;
                $result['errors'][] = "Missing required field: {$field}";
            }
        }
        
        return $result;
    }

    /**
     * Validate WebSite schema
     */
    protected function validateWebSiteSchema(array $schema, array $result): array
    {
        $required = ['name', 'url'];
        $recommended = ['potentialAction'];
        
        foreach ($required as $field) {
            if (!isset($schema[$field])) {
                $result['valid'] = false;
                $result['errors'][] = "Missing required field: {$field}";
            }
        }
        
        return $result;
    }

    /**
     * Validate Organization schema
     */
    protected function validateOrganizationSchema(array $schema, array $result): array
    {
        $required = ['name'];
        $recommended = ['url', 'logo'];
        
        foreach ($required as $field) {
            if (!isset($schema[$field])) {
                $result['valid'] = false;
                $result['errors'][] = "Missing required field: {$field}";
            }
        }
        
        return $result;
    }

    /**
     * Validate Microdata schema
     */
    protected function validateMicrodataSchema(array $schema): array
    {
        return [
            'type' => $schema['@type'] ?? 'Unknown',
            'format' => 'Microdata',
            'valid' => isset($schema['@type']),
            'errors' => [],
            'warnings' => [],
        ];
    }

    /**
     * Get Google Rich Results Test URL (for manual testing)
     */
    public function getGoogleTestUrl(string $url): string
    {
        return $this->googleRichResultsUrl . '?url=' . urlencode($url);
    }

    /**
     * Get Schema.org Validator URL (for manual testing)
     */
    public function getSchemaValidatorUrl(string $url): string
    {
        return $this->schemaOrgValidatorUrl . '?url=' . urlencode($url);
    }
}

