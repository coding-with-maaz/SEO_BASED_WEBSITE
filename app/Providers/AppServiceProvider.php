<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Models\Episode;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Scope episode route model binding to content when content parameter exists
        Route::bind('episode', function ($value, $route) {
            // Only scope if we're in a route that has a content parameter
            if ($route->hasParameter('content')) {
                $content = $route->parameter('content');
                
                // If content is already resolved (Content model instance)
                if ($content instanceof \App\Models\Content) {
                    $episode = Episode::where('id', $value)
                        ->where('content_id', $content->id)
                        ->first();
                    
                    if (!$episode) {
                        abort(404, 'Episode not found for this content.');
                    }
                    
                    return $episode;
                }
                
                // If content is not yet resolved (might be slug or ID), try to resolve it
                if (is_string($content) || is_numeric($content)) {
                    $contentModel = \App\Models\Content::where('slug', $content)
                        ->orWhere('id', $content)
                        ->first();
                    
                    if ($contentModel) {
                        $episode = Episode::where('id', $value)
                            ->where('content_id', $contentModel->id)
                            ->first();
                        
                        if (!$episode) {
                            abort(404, 'Episode not found for this content.');
                        }
                        
                        return $episode;
                    }
                }
            }
            
            // Fallback to standard resolution
            return Episode::findOrFail($value);
        });
    }
}
