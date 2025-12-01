<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SitemapSubmissionService;
use App\Services\SeoAnalyzerService;
use App\Services\BrokenLinkCheckerService;
use App\Services\RichSnippetsTestService;
use Illuminate\Http\Request;

class SeoToolsController extends Controller
{
    /**
     * Show SEO tools dashboard
     */
    public function index()
    {
        $submissionService = app(SitemapSubmissionService::class);
        $lastSubmission = $submissionService->getLastSubmissionStatus();
        
        return view('admin.seo-tools.index', [
            'lastSubmission' => $lastSubmission,
        ]);
    }

    /**
     * Submit sitemap to search engines
     */
    public function submitSitemap(Request $request, SitemapSubmissionService $service)
    {
        $engine = $request->input('engine');
        
        if ($engine) {
            $results = [$engine => $service->submitTo($engine)];
        } else {
            $results = $service->submitAndSave();
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'results' => $results,
            ]);
        }
        
        return redirect()->route('admin.seo-tools.index')
            ->with('success', 'Sitemap submitted successfully')
            ->with('submission_results', $results);
    }

    /**
     * Check SEO for a URL
     */
    public function checkSeo(Request $request, SeoAnalyzerService $service)
    {
        $request->validate([
            'url' => 'required|url',
        ]);
        
        $url = $request->input('url');
        
        // Clear cache for this URL to force fresh analysis
        $cacheKey = 'seo_analysis_' . md5($url);
        \Illuminate\Support\Facades\Cache::forget($cacheKey);
        
        $analysis = $service->analyzeUrl($url);
        
        if ($request->expectsJson()) {
            return response()->json($analysis);
        }
        
        return view('admin.seo-tools.seo-check', [
            'analysis' => $analysis,
            'url' => $url,
        ]);
    }

    /**
     * Check broken links
     */
    public function checkBrokenLinks(Request $request, BrokenLinkCheckerService $service)
    {
        $request->validate([
            'url' => 'nullable|url',
            'page' => 'nullable|url',
            'sitemap' => 'nullable|boolean',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);
        
        $url = $request->input('url');
        $page = $request->input('page');
        $sitemap = $request->input('sitemap');
        $limit = (int) ($request->input('limit') ?? 50); // Default to 50 URLs max
        
        if ($sitemap) {
            $results = $service->checkSitemapUrls($limit);
        } elseif ($page) {
            $pageResult = $service->checkPageLinks($page);
            $results = $pageResult['links'] ?? [];
        } elseif ($url) {
            $results = [$service->checkUrl($url)];
        } else {
            return back()->withErrors(['url' => 'Please provide a URL, page, or select sitemap']);
        }
        
        $summary = $service->getBrokenLinksSummary($results);
        
        if ($request->expectsJson()) {
            return response()->json([
                'summary' => $summary,
                'results' => $results,
            ]);
        }
        
        return view('admin.seo-tools.broken-links', [
            'summary' => $summary,
            'results' => $results,
            'limit' => $sitemap ? $limit : null,
        ]);
    }

    /**
     * Test rich snippets
     */
    public function testRichSnippets(Request $request, RichSnippetsTestService $service)
    {
        $request->validate([
            'url' => 'required|url',
        ]);
        
        $url = $request->input('url');
        
        // Clear cache for this URL to force fresh analysis
        $cacheKey = 'rich_snippets_google_' . md5($url);
        \Illuminate\Support\Facades\Cache::forget($cacheKey);
        
        $result = $service->testWithGoogle($url);
        
        if ($request->expectsJson()) {
            return response()->json($result);
        }
        
        return view('admin.seo-tools.rich-snippets', [
            'result' => $result,
            'url' => $url,
            'googleTestUrl' => $service->getGoogleTestUrl($url),
            'schemaValidatorUrl' => $service->getSchemaValidatorUrl($url),
        ]);
    }

    /**
     * Get sitemap submission history
     */
    public function submissionHistory(SitemapSubmissionService $service)
    {
        $history = $service->getSubmissionHistory();
        
        return response()->json([
            'history' => $history,
        ]);
    }
}

