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
        
        try {
            $validated = $request->validate([
                'server_name' => 'required|string|max:255',
                'quality' => 'nullable|string|max:50',
                'download_link' => 'nullable|string|max:2048',
                'watch_link' => 'nullable|string|max:2048',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        // Convert empty strings to null for nullable fields
        if (isset($validated['download_link']) && empty(trim($validated['download_link']))) {
            $validated['download_link'] = null;
        }
        if (isset($validated['watch_link']) && empty(trim($validated['watch_link']))) {
            $validated['watch_link'] = null;
        }
        
        // Validate URLs only if they are not empty
        if (!empty($validated['download_link']) && !filter_var($validated['download_link'], FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => false,
                'message' => 'The download link must be a valid URL.',
                'errors' => ['download_link' => ['The download link must be a valid URL.']],
            ], 422);
        }
        if (!empty($validated['watch_link']) && !filter_var($validated['watch_link'], FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => false,
                'message' => 'The watch link must be a valid URL.',
                'errors' => ['watch_link' => ['The watch link must be a valid URL.']],
            ], 422);
        }

        $validated['episode_id'] = $episode->id;
        $validated['is_active'] = $request->boolean('is_active', false);

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
        
        // Debug: Log all request data
        \Log::info('EpisodeServer Update Request', [
            'all' => $request->all(),
            'server_name' => $request->input('server_name'),
            'has_server_name' => $request->has('server_name'),
            'method' => $request->method(),
        ]);
        
        try {
            $validated = $request->validate([
                'server_name' => 'required|string|max:255',
                'quality' => 'nullable|string|max:50',
                'download_link' => 'nullable|string|max:2048',
                'watch_link' => 'nullable|string|max:2048',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
                'debug' => [
                    'request_all' => $request->all(),
                    'request_method' => $request->method(),
                    'has_server_name' => $request->has('server_name'),
                    'server_name_value' => $request->input('server_name'),
                ],
            ], 422);
        }

        // Convert empty strings to null for nullable fields
        if (isset($validated['download_link']) && empty(trim($validated['download_link']))) {
            $validated['download_link'] = null;
        }
        if (isset($validated['watch_link']) && empty(trim($validated['watch_link']))) {
            $validated['watch_link'] = null;
        }
        
        // Validate URLs only if they are not empty
        if (!empty($validated['download_link']) && !filter_var($validated['download_link'], FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => false,
                'message' => 'The download link must be a valid URL.',
                'errors' => ['download_link' => ['The download link must be a valid URL.']],
            ], 422);
        }
        if (!empty($validated['watch_link']) && !filter_var($validated['watch_link'], FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => false,
                'message' => 'The watch link must be a valid URL.',
                'errors' => ['watch_link' => ['The watch link must be a valid URL.']],
            ], 422);
        }

        $validated['is_active'] = $request->boolean('is_active', false);

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

