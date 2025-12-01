<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ShareController extends Controller
{
    /**
     * Track share events
     */
    public function track(Request $request)
    {
        $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url|max:500',
            'timestamp' => 'nullable|date',
        ]);

        try {
            $platform = $request->input('platform');
            $url = $request->input('url');
            $timestamp = $request->input('timestamp', now()->toISOString());
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();

            // Log the share event
            Log::info('Share tracked', [
                'platform' => $platform,
                'url' => $url,
                'ip' => $ipAddress,
                'user_agent' => $userAgent,
                'timestamp' => $timestamp,
            ]);

            // Cache share count for analytics (optional)
            $cacheKey = "share_count:{$platform}:{$url}";
            Cache::increment($cacheKey, 1);
            Cache::put($cacheKey, Cache::get($cacheKey, 0), now()->addDays(30));

            // You can also store in database if needed
            // Share::create([...]);

            return response()->json([
                'success' => true,
                'message' => 'Share tracked successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Share tracking failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to track share',
            ], 500);
        }
    }

    /**
     * Get share statistics (optional - for analytics)
     */
    public function stats(Request $request)
    {
        $url = $request->input('url');
        
        if (!$url) {
            return response()->json([
                'success' => false,
                'message' => 'URL is required',
            ], 400);
        }

        $platforms = ['facebook', 'twitter', 'whatsapp', 'instagram', 'threads', 'tiktok', 'telegram', 'reddit', 'linkedin', 'pinterest', 'copy'];
        $stats = [];

        foreach ($platforms as $platform) {
            $cacheKey = "share_count:{$platform}:{$url}";
            $stats[$platform] = Cache::get($cacheKey, 0);
        }

        return response()->json([
            'success' => true,
            'url' => $url,
            'stats' => $stats,
            'total' => array_sum($stats),
        ], 200);
    }
}

