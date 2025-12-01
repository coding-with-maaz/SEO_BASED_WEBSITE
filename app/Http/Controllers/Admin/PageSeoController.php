<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSeo;
use Illuminate\Http\Request;

class PageSeoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $availablePageKeys = PageSeo::getAvailablePageKeys();
        $pageSeos = PageSeo::orderBy('page_name', 'asc')->get();
        
        // Create a map of existing page keys
        $existingPageKeys = $pageSeos->pluck('page_key')->toArray();
        
        return view('admin.page-seo.index', compact('pageSeos', 'availablePageKeys', 'existingPageKeys'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $availablePageKeys = PageSeo::getAvailablePageKeys();
        $existingPageKeys = PageSeo::pluck('page_key')->toArray();
        $availablePageKeys = array_diff_key($availablePageKeys, array_flip($existingPageKeys));
        
        $selectedPageKey = $request->get('page_key');

        return view('admin.page-seo.create', compact('availablePageKeys', 'selectedPageKey'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_key' => 'required|string|unique:page_seos,page_key|max:255',
            'page_name' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'meta_robots' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'og_type' => 'nullable|string|max:255',
            'og_url' => 'nullable|url|max:500',
            'twitter_card' => 'nullable|string|max:255',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:500',
            'schema_markup' => 'nullable|json',
            'hreflang_tags' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        // Convert JSON strings to proper format
        if (isset($validated['schema_markup']) && is_string($validated['schema_markup'])) {
            $validated['schema_markup'] = json_encode(json_decode($validated['schema_markup'], true));
        }
        if (isset($validated['hreflang_tags']) && is_string($validated['hreflang_tags'])) {
            $validated['hreflang_tags'] = json_encode(json_decode($validated['hreflang_tags'], true));
        }

        $validated['is_active'] = $request->has('is_active');

        PageSeo::create($validated);

        return redirect()->route('admin.page-seo.index')
            ->with('success', 'Page SEO settings created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PageSeo $pageSeo)
    {
        return redirect()->route('admin.page-seo.edit', $pageSeo);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PageSeo $pageSeo)
    {
        return view('admin.page-seo.edit', compact('pageSeo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PageSeo $pageSeo)
    {
        $validated = $request->validate([
            'page_name' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'meta_robots' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'og_type' => 'nullable|string|max:255',
            'og_url' => 'nullable|url|max:500',
            'twitter_card' => 'nullable|string|max:255',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:500',
            'schema_markup' => 'nullable|json',
            'hreflang_tags' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        // Convert JSON strings to proper format
        if (isset($validated['schema_markup']) && is_string($validated['schema_markup'])) {
            $validated['schema_markup'] = json_encode(json_decode($validated['schema_markup'], true));
        }
        if (isset($validated['hreflang_tags']) && is_string($validated['hreflang_tags'])) {
            $validated['hreflang_tags'] = json_encode(json_decode($validated['hreflang_tags'], true));
        }

        $validated['is_active'] = $request->has('is_active');

        $pageSeo->update($validated);

        return redirect()->route('admin.page-seo.index')
            ->with('success', 'Page SEO settings updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PageSeo $pageSeo)
    {
        $pageSeo->delete();

        return redirect()->route('admin.page-seo.index')
            ->with('success', 'Page SEO settings deleted successfully.');
    }
}

