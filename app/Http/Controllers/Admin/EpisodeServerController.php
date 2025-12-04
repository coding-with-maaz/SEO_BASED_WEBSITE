<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Episode;
use App\Models\EpisodeServer;
use Illuminate\Http\Request;

class EpisodeServerController extends Controller
{
    /**
     * Get servers for an episode (AJAX).
     */
    public function index(Content $content, Episode $episode)
    {
        // Verify episode belongs to content (route binding should handle this, but double-check)
        if ($episode->content_id !== $content->id) {
            return response()->json([
                'success' => false,
                'message' => 'Episode does not belong to this content.',
            ], 404);
        }
        
        $servers = $episode->servers()->orderBy('sort_order', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'servers' => $servers,
        ]);
    }

    /**
     * Store a newly created server for an episode.
     */
    public function store(Request $request, Content $content, Episode $episode)
    {
        // Verify episode belongs to content
        if ($episode->content_id !== $content->id) {
            return response()->json([
                'success' => false,
                'message' => 'Episode does not belong to this content.',
            ], 404);
        }
        
        $validated = $request->validate([
            'server_name' => 'required|string|max:255',
            'quality' => 'nullable|string|max:50',
            'download_link' => 'nullable|url',
            'watch_link' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['episode_id'] = $episode->id;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $server = EpisodeServer::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Server added successfully.',
                'server' => $server,
            ]);
        }

        return redirect()->route('admin.episodes.index', $content)
            ->with('success', 'Server added successfully.');
    }

    /**
     * Update the specified server.
     */
    public function update(Request $request, Content $content, Episode $episode, EpisodeServer $server)
    {
        // Verify episode belongs to content
        if ($episode->content_id !== $content->id) {
            return response()->json([
                'success' => false,
                'message' => 'Episode does not belong to this content.',
            ], 404);
        }
        
        // Verify server belongs to episode
        if ($server->episode_id !== $episode->id) {
            return response()->json([
                'success' => false,
                'message' => 'Server does not belong to this episode.',
            ], 404);
        }
        
        $validated = $request->validate([
            'server_name' => 'required|string|max:255',
            'quality' => 'nullable|string|max:50',
            'download_link' => 'nullable|url',
            'watch_link' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $server->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Server updated successfully.',
                'server' => $server->fresh(),
            ]);
        }

        return redirect()->route('admin.episodes.index', $content)
            ->with('success', 'Server updated successfully.');
    }

    /**
     * Remove the specified server.
     */
    public function destroy(Request $request, Content $content, Episode $episode, EpisodeServer $server)
    {
        // Verify episode belongs to content
        if ($episode->content_id !== $content->id) {
            return response()->json([
                'success' => false,
                'message' => 'Episode does not belong to this content.',
            ], 404);
        }
        
        // Verify server belongs to episode
        if ($server->episode_id !== $episode->id) {
            return response()->json([
                'success' => false,
                'message' => 'Server does not belong to this episode.',
            ], 404);
        }
        
        $server->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Server deleted successfully.',
            ]);
        }

        return redirect()->route('admin.episodes.index', $content)
            ->with('success', 'Server deleted successfully.');
    }
}

