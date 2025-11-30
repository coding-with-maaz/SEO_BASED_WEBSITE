<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use Illuminate\Http\Request;

class SeoPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seoPages = SeoPage::orderBy('page_name', 'asc')->paginate(20);
        $availablePages = SeoPage::getAvailablePageKeys();
        
        return view('admin.seo-pages.index', compact('seoPages', 'availablePages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availablePages = SeoPage::getAvailablePageKeys();
        $existingPageKeys = SeoPage::pluck('page_key')->toArray();
        $availablePages = array_diff_key($availablePages, array_flip($existingPageKeys));
        
        return view('admin.seo-pages.create', compact('availablePages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateSeoData($request);
        
        SeoPage::create($validated);

        return redirect()->route('admin.seo-pages.index')
            ->with('success', 'SEO page created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SeoPage $seoPage)
    {
        return view('admin.seo-pages.edit', compact('seoPage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeoPage $seoPage)
    {
        $validated = $this->validateSeoData($request, false);

        $seoPage->update($validated);

        return redirect()->route('admin.seo-pages.index')
            ->with('success', 'SEO page updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SeoPage $seoPage)
    {
        $seoPage->delete();

        return redirect()->route('admin.seo-pages.index')
            ->with('success', 'SEO page deleted successfully.');
    }

    /**
     * Validate SEO data
     */
    protected function validateSeoData(Request $request, bool $includePageKey = true): array
    {
        $rules = [
            'page_name' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'meta_robots' => 'nullable|string|max:100',
            'meta_author' => 'nullable|string|max:255',
            'meta_language' => 'nullable|string|max:10',
            'meta_geo_region' => 'nullable|string|max:50',
            'meta_geo_placename' => 'nullable|string|max:255',
            'meta_geo_position_lat' => 'nullable|numeric|between:-90,90',
            'meta_geo_position_lon' => 'nullable|numeric|between:-180,180',
            'meta_revisit_after' => 'nullable|string|max:50',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|url|max:500',
            'og_url' => 'nullable|url|max:500',
            'og_type' => 'nullable|string|max:50',
            'og_locale' => 'nullable|string|max:10',
            'og_site_name' => 'nullable|string|max:255',
            'og_video_url' => 'nullable|url|max:500',
            'og_video_duration' => 'nullable|integer|min:0',
            'og_video_type' => 'nullable|string|max:50',
            'twitter_card' => 'nullable|string|in:summary,summary_large_image',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|url|max:500',
            'twitter_site' => 'nullable|string|max:100',
            'twitter_creator' => 'nullable|string|max:100',
            'canonical_url' => 'nullable|url|max:500',
            'hreflang_tags' => 'nullable|json',
            'schema_markup' => 'nullable|json',
            'additional_meta_tags' => 'nullable|json',
            'breadcrumb_schema' => 'nullable|json',
            'preconnect_domains' => 'nullable|string|max:500',
            'dns_prefetch_domains' => 'nullable|string|max:500',
            'enable_amp' => 'nullable|boolean',
            'amp_url' => 'nullable|url|max:500',
            'is_active' => 'nullable|boolean',
        ];

        if ($includePageKey) {
            $rules['page_key'] = 'required|string|unique:seo_pages,page_key|max:255';
        }

        $validated = $request->validate($rules);
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['enable_amp'] = $validated['enable_amp'] ?? false;
        
        // Handle JSON fields - convert JSON string to array if provided
        $jsonFields = ['schema_markup', 'breadcrumb_schema', 'additional_meta_tags', 'hreflang_tags'];
        foreach ($jsonFields as $field) {
            if (isset($validated[$field]) && is_string($validated[$field])) {
                $decoded = json_decode($validated[$field], true);
                $validated[$field] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
            }
        }

        return $validated;
    }
}

