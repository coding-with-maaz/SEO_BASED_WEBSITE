<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SeoAnalyzerService
{
    protected $siteUrl;

    public function __construct()
    {
        $this->siteUrl = rtrim(config('app.url', url('/')), '/');
    }

    /**
     * Analyze SEO for a specific URL
     */
    public function analyzeUrl(string $url): array
    {
        $cacheKey = 'seo_analysis_' . md5($url);
        
        return Cache::remember($cacheKey, 3600, function () use ($url) {
            try {
                // Increase timeout for localhost URLs
                $timeout = (str_contains($url, '127.0.0.1') || str_contains($url, 'localhost')) ? 30 : 15;
                $response = Http::timeout($timeout)->get($url);
                
                if (!$response->successful()) {
                    return [
                        'success' => false,
                        'error' => "HTTP {$response->status()}",
                        'url' => $url,
                    ];
                }

                $html = $response->body();
                return $this->analyzeHtml($html, $url);
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
     * Analyze HTML content for SEO
     */
    protected function analyzeHtml(string $html, string $url): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $analysis = [
            'url' => $url,
            'success' => true,
            'score' => 0,
            'max_score' => 100,
            'checks' => [],
            'recommendations' => [],
        ];

        $checks = [
            'title' => $this->checkTitle($xpath),
            'meta_description' => $this->checkMetaDescription($xpath),
            'meta_keywords' => $this->checkMetaKeywords($xpath),
            'h1' => $this->checkH1($xpath),
            'headings' => $this->checkHeadings($xpath),
            'images' => $this->checkImages($xpath),
            'links' => $this->checkLinks($xpath),
            'canonical' => $this->checkCanonical($xpath, $url),
            'open_graph' => $this->checkOpenGraph($xpath),
            'twitter_cards' => $this->checkTwitterCards($xpath),
            'schema' => $this->checkSchema($html),
            'mobile_friendly' => $this->checkMobileFriendly($xpath),
            'page_speed' => $this->checkPageSpeed($html),
        ];

        $analysis['checks'] = $checks;
        $analysis['score'] = $this->calculateScore($checks);
        $analysis['recommendations'] = $this->generateRecommendations($checks);

        return $analysis;
    }

    /**
     * Check title tag
     */
    protected function checkTitle($xpath): array
    {
        $titleNodes = $xpath->query('//title');
        $hasTitle = $titleNodes->length > 0;
        $title = $hasTitle ? trim($titleNodes->item(0)->textContent) : '';
        $length = mb_strlen($title);
        
        $score = 0;
        $issues = [];
        
        if (!$hasTitle || empty($title)) {
            $issues[] = 'Missing title tag';
        } else {
            $score += 10;
            
            if ($length < 30) {
                $issues[] = 'Title is too short (recommended: 30-60 characters)';
            } elseif ($length > 60) {
                $issues[] = 'Title is too long (recommended: 30-60 characters)';
            } else {
                $score += 5;
            }
        }

        return [
            'passed' => $score >= 10,
            'score' => $score,
            'max_score' => 15,
            'title' => $title,
            'length' => $length,
            'issues' => $issues,
        ];
    }

    /**
     * Check meta description
     */
    protected function checkMetaDescription($xpath): array
    {
        $metaNodes = $xpath->query('//meta[@name="description"]/@content');
        $hasDescription = $metaNodes->length > 0;
        $description = $hasDescription ? trim($metaNodes->item(0)->value) : '';
        $length = mb_strlen($description);
        
        $score = 0;
        $issues = [];
        
        if (!$hasDescription || empty($description)) {
            $issues[] = 'Missing meta description';
        } else {
            $score += 10;
            
            if ($length < 120) {
                $issues[] = 'Meta description is too short (recommended: 120-160 characters)';
            } elseif ($length > 160) {
                $issues[] = 'Meta description is too long (recommended: 120-160 characters)';
            } else {
                $score += 5;
            }
        }

        return [
            'passed' => $score >= 10,
            'score' => $score,
            'max_score' => 15,
            'description' => $description,
            'length' => $length,
            'issues' => $issues,
        ];
    }

    /**
     * Check meta keywords
     */
    protected function checkMetaKeywords($xpath): array
    {
        $metaNodes = $xpath->query('//meta[@name="keywords"]/@content');
        $hasKeywords = $metaNodes->length > 0;
        $keywords = $hasKeywords ? trim($metaNodes->item(0)->value) : '';
        
        $score = $hasKeywords && !empty($keywords) ? 5 : 0;
        $issues = [];
        
        if (!$hasKeywords || empty($keywords)) {
            $issues[] = 'Meta keywords tag is missing (optional but recommended)';
        }

        return [
            'passed' => $score > 0,
            'score' => $score,
            'max_score' => 5,
            'keywords' => $keywords,
            'issues' => $issues,
        ];
    }

    /**
     * Check H1 tag
     */
    protected function checkH1($xpath): array
    {
        $h1Nodes = $xpath->query('//h1');
        $h1Count = $h1Nodes->length;
        $h1 = $h1Count > 0 ? trim($h1Nodes->item(0)->textContent) : '';
        
        $score = 0;
        $issues = [];
        
        if ($h1Count === 0) {
            $issues[] = 'Missing H1 tag';
        } elseif ($h1Count > 1) {
            $issues[] = 'Multiple H1 tags found (recommended: 1)';
        } else {
            $score = 10;
        }

        return [
            'passed' => $score > 0,
            'score' => $score,
            'max_score' => 10,
            'count' => $h1Count,
            'text' => $h1,
            'issues' => $issues,
        ];
    }

    /**
     * Check heading structure
     */
    protected function checkHeadings($xpath): array
    {
        $headings = [];
        for ($i = 1; $i <= 6; $i++) {
            $nodes = $xpath->query("//h{$i}");
            $headings["h{$i}"] = $nodes->length;
        }
        
        $score = 5; // Base score for having headings
        $issues = [];
        
        if ($headings['h1'] === 0) {
            $score = 0;
            $issues[] = 'No H1 tag found';
        }

        return [
            'passed' => $score > 0,
            'score' => $score,
            'max_score' => 5,
            'headings' => $headings,
            'issues' => $issues,
        ];
    }

    /**
     * Check images for alt text
     */
    protected function checkImages($xpath): array
    {
        $imgNodes = $xpath->query('//img');
        $totalImages = $imgNodes->length;
        $imagesWithAlt = 0;
        $imagesWithoutAlt = [];
        
        foreach ($imgNodes as $img) {
            $alt = $img->getAttribute('alt');
            if (!empty($alt)) {
                $imagesWithAlt++;
            } else {
                $src = $img->getAttribute('src');
                if (!empty($src)) {
                    $imagesWithoutAlt[] = $src;
                }
            }
        }
        
        $score = $totalImages > 0 
            ? min(10, round(($imagesWithAlt / $totalImages) * 10))
            : 5; // No images is okay
        
        $issues = [];
        if ($totalImages > 0 && $imagesWithAlt < $totalImages) {
            $missing = $totalImages - $imagesWithAlt;
            $issues[] = "{$missing} image(s) missing alt text";
        }

        return [
            'passed' => $score >= 7,
            'score' => $score,
            'max_score' => 10,
            'total_images' => $totalImages,
            'images_with_alt' => $imagesWithAlt,
            'images_without_alt' => $imagesWithoutAlt,
            'issues' => $issues,
        ];
    }

    /**
     * Check internal and external links
     */
    protected function checkLinks($xpath): array
    {
        $linkNodes = $xpath->query('//a[@href]');
        $totalLinks = $linkNodes->length;
        $internalLinks = 0;
        $externalLinks = 0;
        $linksWithoutText = [];
        
        foreach ($linkNodes as $link) {
            $href = $link->getAttribute('href');
            $text = trim($link->textContent);
            
            if (empty($text)) {
                $linksWithoutText[] = $href;
            }
            
            if (Str::startsWith($href, ['http://', 'https://'])) {
                if (Str::contains($href, $this->siteUrl)) {
                    $internalLinks++;
                } else {
                    $externalLinks++;
                }
            } else {
                $internalLinks++;
            }
        }
        
        $score = min(10, $totalLinks > 0 ? 10 : 5);
        $issues = [];
        
        if ($totalLinks === 0) {
            $issues[] = 'No links found on page';
        } elseif (count($linksWithoutText) > 0) {
            $issues[] = count($linksWithoutText) . ' link(s) without text';
        }

        return [
            'passed' => $score >= 7,
            'score' => $score,
            'max_score' => 10,
            'total_links' => $totalLinks,
            'internal_links' => $internalLinks,
            'external_links' => $externalLinks,
            'links_without_text' => $linksWithoutText,
            'issues' => $issues,
        ];
    }

    /**
     * Check canonical URL
     */
    protected function checkCanonical($xpath, string $url): array
    {
        $canonicalNodes = $xpath->query('//link[@rel="canonical"]/@href');
        $hasCanonical = $canonicalNodes->length > 0;
        $canonical = $hasCanonical ? trim($canonicalNodes->item(0)->value) : '';
        
        $score = $hasCanonical ? 10 : 0;
        $issues = [];
        
        if (!$hasCanonical) {
            $issues[] = 'Missing canonical URL';
        } elseif ($canonical !== $url) {
            $issues[] = 'Canonical URL does not match current URL';
        }

        return [
            'passed' => $score > 0,
            'score' => $score,
            'max_score' => 10,
            'canonical' => $canonical,
            'issues' => $issues,
        ];
    }

    /**
     * Check Open Graph tags
     */
    protected function checkOpenGraph($xpath): array
    {
        $ogTags = ['og:title', 'og:description', 'og:image', 'og:url', 'og:type'];
        $foundTags = [];
        
        foreach ($ogTags as $tag) {
            $nodes = $xpath->query("//meta[@property='{$tag}']");
            if ($nodes->length > 0) {
                $foundTags[] = $tag;
            }
        }
        
        $score = round((count($foundTags) / count($ogTags)) * 10);
        $issues = [];
        
        $missing = array_diff($ogTags, $foundTags);
        if (!empty($missing)) {
            $issues[] = 'Missing Open Graph tags: ' . implode(', ', $missing);
        }

        return [
            'passed' => $score >= 7,
            'score' => $score,
            'max_score' => 10,
            'found_tags' => $foundTags,
            'missing_tags' => array_diff($ogTags, $foundTags),
            'issues' => $issues,
        ];
    }

    /**
     * Check Twitter Card tags
     */
    protected function checkTwitterCards($xpath): array
    {
        $twitterTags = ['twitter:card', 'twitter:title', 'twitter:description', 'twitter:image'];
        $foundTags = [];
        
        foreach ($twitterTags as $tag) {
            $nodes = $xpath->query("//meta[@name='{$tag}']");
            if ($nodes->length > 0) {
                $foundTags[] = $tag;
            }
        }
        
        $score = round((count($foundTags) / count($twitterTags)) * 10);
        $issues = [];
        
        $missing = array_diff($twitterTags, $foundTags);
        if (!empty($missing)) {
            $issues[] = 'Missing Twitter Card tags: ' . implode(', ', $missing);
        }

        return [
            'passed' => $score >= 7,
            'score' => $score,
            'max_score' => 10,
            'found_tags' => $foundTags,
            'missing_tags' => array_diff($twitterTags, $foundTags),
            'issues' => $issues,
        ];
    }

    /**
     * Check Schema.org structured data
     */
    protected function checkSchema(string $html): array
    {
        $hasJsonLd = strpos($html, 'application/ld+json') !== false;
        $hasMicrodata = strpos($html, 'itemscope') !== false;
        $hasRdfa = strpos($html, 'typeof') !== false;
        
        $score = 0;
        if ($hasJsonLd) $score += 7;
        if ($hasMicrodata) $score += 2;
        if ($hasRdfa) $score += 1;
        
        $issues = [];
        if (!$hasJsonLd && !$hasMicrodata && !$hasRdfa) {
            $issues[] = 'No structured data (Schema.org) found';
        }

        return [
            'passed' => $score > 0,
            'score' => $score,
            'max_score' => 10,
            'has_json_ld' => $hasJsonLd,
            'has_microdata' => $hasMicrodata,
            'has_rdfa' => $hasRdfa,
            'issues' => $issues,
        ];
    }

    /**
     * Check mobile-friendly (viewport meta tag)
     */
    protected function checkMobileFriendly($xpath): array
    {
        $viewportNodes = $xpath->query('//meta[@name="viewport"]');
        $hasViewport = $viewportNodes->length > 0;
        
        $score = $hasViewport ? 10 : 0;
        $issues = [];
        
        if (!$hasViewport) {
            $issues[] = 'Missing viewport meta tag (required for mobile-friendly)';
        }

        return [
            'passed' => $score > 0,
            'score' => $score,
            'max_score' => 10,
            'has_viewport' => $hasViewport,
            'issues' => $issues,
        ];
    }

    /**
     * Check page speed indicators
     */
    protected function checkPageSpeed(string $html): array
    {
        $size = strlen($html);
        $sizeKB = round($size / 1024, 2);
        
        $score = 10;
        $issues = [];
        
        if ($sizeKB > 500) {
            $score = 5;
            $issues[] = "Page size is large ({$sizeKB} KB). Consider optimizing.";
        } elseif ($sizeKB > 1000) {
            $score = 0;
            $issues[] = "Page size is very large ({$sizeKB} KB). Optimization required.";
        }

        return [
            'passed' => $score >= 7,
            'score' => $score,
            'max_score' => 10,
            'size_kb' => $sizeKB,
            'issues' => $issues,
        ];
    }

    /**
     * Calculate overall SEO score
     */
    protected function calculateScore(array $checks): int
    {
        $totalScore = 0;
        $maxScore = 0;
        
        foreach ($checks as $check) {
            if (isset($check['score']) && isset($check['max_score'])) {
                $totalScore += $check['score'];
                $maxScore += $check['max_score'];
            }
        }
        
        return $maxScore > 0 ? round(($totalScore / $maxScore) * 100) : 0;
    }

    /**
     * Generate recommendations based on checks
     */
    protected function generateRecommendations(array $checks): array
    {
        $recommendations = [];
        
        foreach ($checks as $checkName => $check) {
            if (isset($check['issues']) && !empty($check['issues'])) {
                foreach ($check['issues'] as $issue) {
                    $recommendations[] = [
                        'check' => $checkName,
                        'issue' => $issue,
                        'priority' => $this->getPriority($checkName),
                    ];
                }
            }
        }
        
        // Sort by priority
        usort($recommendations, function($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
        
        return $recommendations;
    }

    /**
     * Get priority for recommendations
     */
    protected function getPriority(string $checkName): int
    {
        $priorities = [
            'title' => 10,
            'meta_description' => 9,
            'h1' => 8,
            'canonical' => 8,
            'open_graph' => 7,
            'schema' => 7,
            'images' => 6,
            'twitter_cards' => 6,
            'mobile_friendly' => 8,
            'headings' => 5,
            'links' => 5,
            'meta_keywords' => 3,
            'page_speed' => 4,
        ];
        
        return $priorities[$checkName] ?? 5;
    }
}

