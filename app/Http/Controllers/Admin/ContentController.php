<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contents = Content::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.contents.index', compact('contents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contentTypes = Content::getContentTypes();
        $dubbingLanguages = Content::getDubbingLanguages();
        return view('admin.contents.create', compact('contentTypes', 'dubbingLanguages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'content_type' => 'required|string|in:custom,tmdb',
            'tmdb_id' => 'nullable|string',
            'poster_path' => 'nullable|string',
            'backdrop_path' => 'nullable|string',
            'release_date' => 'nullable|date',
            'rating' => 'nullable|numeric|min:0|max:10',
            'episode_count' => 'nullable|integer|min:0',
            'status' => 'required|string|in:published,draft,upcoming',
            'genres' => 'nullable|array',
            'cast' => 'nullable|array',
            'language' => 'nullable|string',
            'dubbing_language' => 'nullable|string',
            'download_link' => 'nullable|url',
            'watch_link' => 'nullable|url',
            'sort_order' => 'nullable|integer',
            'is_featured' => 'boolean',
        ]);

        Content::create($validated);

        return redirect()->route('admin.contents.index')
            ->with('success', 'Content created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Content $content)
    {
        return view('admin.contents.show', compact('content'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $content)
    {
        $contentTypes = Content::getContentTypes();
        $dubbingLanguages = Content::getDubbingLanguages();
        return view('admin.contents.edit', compact('content', 'contentTypes', 'dubbingLanguages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $content)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'content_type' => 'required|string|in:custom,tmdb',
            'tmdb_id' => 'nullable|string',
            'poster_path' => 'nullable|string',
            'backdrop_path' => 'nullable|string',
            'release_date' => 'nullable|date',
            'rating' => 'nullable|numeric|min:0|max:10',
            'episode_count' => 'nullable|integer|min:0',
            'status' => 'required|string|in:published,draft,upcoming',
            'genres' => 'nullable|array',
            'cast' => 'nullable|array',
            'language' => 'nullable|string',
            'dubbing_language' => 'nullable|string',
            'download_link' => 'nullable|url',
            'watch_link' => 'nullable|url',
            'sort_order' => 'nullable|integer',
            'is_featured' => 'boolean',
        ]);

        $content->update($validated);

        return redirect()->route('admin.contents.index')
            ->with('success', 'Content updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        $content->delete();

        return redirect()->route('admin.contents.index')
            ->with('success', 'Content deleted successfully.');
    }
}
