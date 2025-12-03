@extends('layouts.app')

@section('title', (isset($isCustom) && $isCustom ? ($content->title ?? 'TV Show') : ($tvShow['name'] ?? 'TV Show')) . ' - Nazaarabox')

@section('content')
@php
    if (isset($isCustom) && $isCustom) {
        $title = $content->title;
        $originalTitle = $content->title;
        $rating = $content->rating ?? 0;
        $views = $content->views ?? 0;
        $status = $content->series_status ?? 'ongoing';
        $network = $content->network;
        $releaseDate = $content->release_date;
        $endDate = $content->end_date;
        $duration = $content->duration;
        $country = $content->country;
        $type = ucfirst(str_replace('_', ' ', $content->type));
        $director = $content->director;
        $genres = $content->genres ?? [];
        $cast = $content->castMembers ? $content->castMembers->map(function($castMember) {
            return [
                'id' => $castMember->id,
                'slug' => $castMember->slug,
                'name' => $castMember->name,
                'character' => $castMember->pivot->character ?? '',
                'profile_path' => $castMember->profile_path,
            ];
        })->toArray() : [];
        $description = $content->description;
        $posterPath = $content->poster_path;
        $backdropPath = $content->backdrop_path;
        $episodes = $content->episodes ?? collect([]);
        $currentEpisodes = $episodes->count();
        $episodeCount = $content->episode_count ?? $currentEpisodes;
    } else {
        $views = 0; // TMDB content doesn't have views
        $backdropPath = $tvShow['backdrop_path'] ?? null;
        $title = $tvShow['name'] ?? 'Unknown';
        $originalTitle = $tvShow['original_name'] ?? $title;
        $rating = $tvShow['vote_average'] ?? 0;
        $status = 'ongoing'; // TMDB doesn't provide status, default to ongoing
        $network = isset($tvShow['networks'][0]) ? $tvShow['networks'][0]['name'] : null;
        $releaseDate = $tvShow['first_air_date'] ?? null;
        $endDate = $tvShow['last_air_date'] ?? null;
        $duration = isset($tvShow['episode_run_time'][0]) ? $tvShow['episode_run_time'][0] : null;
        $country = isset($tvShow['origin_country'][0]) ? $tvShow['origin_country'][0] : null;
        $type = 'Drama'; // Default, can be enhanced
        $episodeCount = $tvShow['number_of_episodes'] ?? 0;
        $director = null; // TMDB doesn't provide director for TV shows
        $genres = $tvShow['genres'] ?? [];
        $cast = $tvShow['credits']['cast'] ?? [];
        $description = $tvShow['overview'] ?? '';
        $posterPath = $tvShow['poster_path'] ?? null;
        $episodes = isset($content) && $content ? $content->episodes : collect([]);
        $currentEpisodes = $tvShow['number_of_episodes'] ?? 0;
    }
    
    // Calculate status display
    $statusDisplay = '';
    if ($status === 'ongoing') {
        $statusDisplay = 'Ongoing - ' . $currentEpisodes . ' / ' . $episodeCount;
    } elseif ($status === 'completed') {
        $statusDisplay = 'Completed - ' . $episodeCount . ' Episodes';
    } else {
        $statusDisplay = ucfirst($status);
    }
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumbs -->
    <x-breadcrumbs :items="[
        ['label' => 'TV Shows', 'url' => route('tv-shows.index')],
        ['label' => $title, 'url' => null]
    ]" />
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Poster -->
        <div class="lg:col-span-1">
            @if(isset($isCustom) && $isCustom)
                @php
                    $posterUrl = null;
                    if ($posterPath) {
                        // Check if it's a TMDB path (starts with /) or content_type is tmdb
                        $contentType = $content->content_type ?? 'custom';
                        if (str_starts_with($posterPath, '/') || in_array($contentType, ['tmdb', 'article'])) {
                            // Use TMDB service for TMDB paths
                            $posterUrl = app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w500');
                        } elseif (str_starts_with($posterPath, 'http')) {
                            // Full URL
                            $posterUrl = $posterPath;
                        } else {
                            // Local storage
                            $posterUrl = asset('storage/' . $posterPath);
                        }
                    }
                @endphp
                <img src="{{ $posterUrl ?? 'https://via.placeholder.com/500x750?text=No+Image' }}" 
                     alt="{{ $title }}" 
                     class="w-full rounded-xl shadow-2xl"
                     onerror="this.src='https://via.placeholder.com/500x750?text=No+Image'">
            @else
                <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w500') }}" 
                     alt="{{ $title }}" 
                     class="w-full rounded-xl shadow-2xl"
                     onerror="this.src='https://via.placeholder.com/500x750?text=No+Image'">
            @endif
        </div>
        
        <!-- Details -->
        <div class="lg:col-span-2">
            <!-- Social Share Buttons -->
            @php
                $shareTitle = $title ?? 'TV Show';
                $shareDescription = $description ?? '';
                $shareImage = null;
                if ($posterPath) {
                    $contentType = isset($content) ? ($content->content_type ?? 'custom') : 'custom';
                    if (str_starts_with($posterPath, '/') || in_array($contentType, ['tmdb', 'article'])) {
                        $shareImage = app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w500');
                    } elseif (str_starts_with($posterPath, 'http')) {
                        $shareImage = $posterPath;
                    } else {
                        $shareImage = asset('storage/' . $posterPath);
                    }
                }
                $shareImage = $shareImage ?? asset('icon.png');
            @endphp
            <x-social-share 
                :url="url()->current()" 
                :title="$shareTitle" 
                :description="$shareDescription"
                :image="$shareImage"
                type="video.tv_show" 
            />
            @if(!(isset($isCustom) && $isCustom && isset($content) && $content->content_type === 'article' && $backdropPath))
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 800;">
                {{ $title }}
            </h1>
            @else
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 800;">
                {{ $title }}
            </h1>
            @endif
            
            @if($originalTitle !== $title)
            <p class="text-lg text-gray-600 dark:!text-text-secondary mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                Original Title: {{ $originalTitle }}
            </p>
            @endif
            
            <div class="flex flex-wrap items-center gap-4 mb-6">
                <div class="flex items-center gap-2 text-yellow-500">
                    <span class="text-2xl">★</span>
                    <span class="text-xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($rating, 1) }}/10</span>
                </div>
                <span class="text-gray-600 dark:!text-text-secondary">•</span>
                <span class="text-yellow-500 font-semibold text-lg dark:!text-yellow-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    {{ $statusDisplay }}
                </span>
                @if($releaseDate)
                <span class="text-gray-600 dark:!text-text-secondary">•</span>
                <span class="text-gray-600 dark:!text-text-secondary">{{ \Carbon\Carbon::parse($releaseDate)->format('Y') }}</span>
                @endif
                @if($duration)
                <span class="text-gray-600 dark:!text-text-secondary">•</span>
                <span class="text-gray-600 dark:!text-text-secondary">{{ $duration }} min/ep</span>
                @endif
                @if(isset($isCustom) && $isCustom && $views > 0)
                <span class="text-gray-600 dark:!text-text-secondary">•</span>
                <div class="flex items-center gap-1 text-gray-600 dark:!text-text-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span class="font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">{{ number_format($views) }} views</span>
                </div>
                @endif
            </div>

            @if(!empty($genres))
            <div class="flex flex-wrap gap-2 mb-6">
                @if(isset($isCustom) && $isCustom)
                    @foreach(is_array($genres) ? $genres : [] as $genre)
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm border border-gray-200 dark:!bg-bg-card dark:!text-text-secondary dark:!border-border-primary" style="font-family: 'Poppins', sans-serif; font-weight: 500;">{{ is_array($genre) ? ($genre['name'] ?? $genre) : $genre }}</span>
                    @endforeach
                @else
                    @foreach($genres as $genre)
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm border border-gray-200 dark:!bg-bg-card dark:!text-text-secondary dark:!border-border-primary" style="font-family: 'Poppins', sans-serif; font-weight: 500;">{{ $genre['name'] }}</span>
                    @endforeach
                @endif
            </div>
            @endif

            @if($description)
            <p class="text-gray-600 dark:!text-text-secondary leading-relaxed mb-6 text-base md:text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                {{ $description }}
            </p>
            @endif
        </div>
    </div>

    <!-- Article Content Section (for article content type) -->
    @if(isset($isCustom) && $isCustom && isset($content) && !empty($content->article_content))
    <div class="bg-white dark:!bg-bg-card border border-gray-200 dark:!border-border-secondary rounded-xl p-6 md:p-8 lg:p-10 mb-8 shadow-sm">
        <div class="article-content max-w-4xl mx-auto">
            {!! $content->article_content !!}
        </div>
    </div>
    @endif

    <!-- Watch/Download Section for Article Content -->
    @if(isset($isCustom) && $isCustom && isset($content) && $content->content_type === 'article')
    @php
        // Get normalized active servers
        $servers = $content->getActiveServers();
        
        // If no servers but watch_link exists, create a default server
        if (empty($servers) && $content->watch_link) {
            $servers = [[
                'id' => 'default',
                'name' => 'Server 1',
                'url' => $content->watch_link,
                'quality' => 'HD',
                'active' => true,
                'sort_order' => 0
            ]];
        }
        
        // Get all download links (from servers and content level)
        $downloadLinks = $content->getAllDownloadLinks();
    @endphp
    
    @if(!empty($servers) || $content->watch_link || !empty($downloadLinks))
    <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:!from-bg-card dark:!to-bg-card-hover border border-gray-200 dark:!border-border-secondary rounded-xl p-8 mb-8 shadow-lg">
        <div class="text-center mb-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:!text-white mb-3" style="font-family: 'Poppins', sans-serif; font-weight: 800;">
                Ready to Watch?
            </h2>
            <p class="text-gray-600 dark:!text-text-secondary text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Click the button below to start streaming
            </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center max-w-2xl mx-auto">
            <!-- Watch Now Button -->
            @if(!empty($servers) || $content->watch_link)
                @php
                    $watchUrl = !empty($servers) ? ($servers[0]['url'] ?? $content->watch_link) : $content->watch_link;
                @endphp
                <a href="{{ $watchUrl }}" 
                   target="_blank"
                   class="group relative w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-accent to-red-700 hover:from-red-700 hover:to-accent text-white font-bold rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center gap-3 overflow-hidden"
                   style="font-family: 'Poppins', sans-serif; font-weight: 700; min-width: 200px;">
                    <!-- Animated Background -->
                    <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform -skew-x-12 group-hover:translate-x-full"></div>
                    
                    <!-- Play Icon -->
                    <svg class="w-6 h-6 relative z-10" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    
                    <!-- Button Text -->
                    <span class="relative z-10 text-lg">Watch Now</span>
                    
                    <!-- Shine Effect -->
                    <div class="absolute inset-0 -top-2 -bottom-2 bg-gradient-to-r from-transparent via-white/30 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </a>
            @endif
            
            <!-- Download Button -->
            @if(!empty($downloadLinks))
                @php
                    $primaryDownload = $downloadLinks[0] ?? null;
                @endphp
                @if($primaryDownload)
                <a href="{{ $primaryDownload['url'] }}" 
                   target="_blank"
                   class="group relative w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-700 hover:from-emerald-700 hover:to-green-600 text-white font-bold rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center gap-3 overflow-hidden"
                   style="font-family: 'Poppins', sans-serif; font-weight: 700; min-width: 200px;">
                    <!-- Animated Background -->
                    <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform -skew-x-12 group-hover:translate-x-full"></div>
                    
                    <!-- Download Icon -->
                    <svg class="w-6 h-6 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    
                    <!-- Button Text -->
                    <span class="relative z-10 text-lg">Download</span>
                    
                    <!-- Shine Effect -->
                    <div class="absolute inset-0 -top-2 -bottom-2 bg-gradient-to-r from-transparent via-white/30 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </a>
                @endif
            @endif
        </div>
        
        <!-- Additional Download Options -->
        @if(!empty($downloadLinks) && count($downloadLinks) > 1)
        <div class="mt-8 pt-6 border-t border-gray-300 dark:!border-border-primary">
            <h3 class="text-lg font-semibold text-gray-900 dark:!text-white mb-4 text-center" style="font-family: 'Poppins', sans-serif; font-weight: 600;">More Download Options</h3>
            <div class="flex flex-wrap gap-3 justify-center">
                @foreach(array_slice($downloadLinks, 1) as $download)
                <a href="{{ $download['url'] }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:!bg-bg-card border-2 border-gray-300 dark:!border-border-primary hover:border-accent text-gray-700 dark:!text-white hover:text-accent font-semibold rounded-lg transition-all duration-300 hover:shadow-md transform hover:scale-105"
                   style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    {{ $download['name'] }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Server Selection (if multiple servers) -->
        @if(count($servers) > 1)
        <div class="mt-6 pt-6 border-t border-gray-300 dark:!border-border-primary">
            <h3 class="text-lg font-semibold text-gray-900 dark:!text-white mb-4 text-center" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Available Servers</h3>
            <div class="flex flex-wrap gap-3 justify-center">
                @foreach($servers as $index => $server)
                    @if(!empty($server['url']))
                    <a href="{{ $server['url'] }}" 
                       target="_blank"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:!bg-bg-card border-2 border-gray-300 dark:!border-border-primary hover:border-accent text-gray-700 dark:!text-white hover:text-accent font-semibold rounded-lg transition-all duration-300 hover:shadow-md transform hover:scale-105"
                       style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        {{ $server['name'] ?? 'Server ' . ($index + 1) }}@if(!empty($server['quality'])) - {{ $server['quality'] }}@endif
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif
    @endif

    <!-- Watch All Episodes Section (if content has watch_link or servers) -->
    @if(isset($isCustom) && $isCustom && isset($content))
    @php
        // Get normalized active servers for content level (all episodes in one)
        $contentServers = $content->getActiveServers();
        
        // If no servers but watch_link exists, create a default server
        if (empty($contentServers) && $content->watch_link) {
            $contentServers = [[
                'id' => 'default',
                'name' => 'Server 1',
                'url' => $content->watch_link,
                'quality' => 'HD',
                'active' => true,
                'sort_order' => 0
            ]];
        }
        
        // Get the first server as default for player
        $defaultContentServer = !empty($contentServers) ? reset($contentServers) : null;
        $currentContentServerUrl = $defaultContentServer['url'] ?? $content->watch_link ?? '';
        
        // Get all download links (from servers and content level)
        $contentDownloadLinks = $content->getAllDownloadLinks();
    @endphp
    
    @if(!empty($contentServers) || $content->watch_link)
    <div class="bg-white border border-gray-200 p-6 mb-8 dark:!bg-bg-card dark:!border-border-secondary rounded-lg">
        <h2 class="text-xl font-bold text-gray-900 mb-4 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Watch All Episodes</h2>
        
        <!-- Video Player Container -->
        <div class="mb-4">
            <div class="relative w-full bg-black rounded-lg overflow-hidden" style="padding-bottom: 56.25%;">
                <iframe id="tvShowPlayer" 
                        src="{{ $currentContentServerUrl }}" 
                        class="absolute top-0 left-0 w-full h-full border-0" 
                        allow="autoplay; fullscreen" 
                        allowfullscreen
                        frameborder="0">
                </iframe>
            </div>
        </div>

        <!-- Server Selection -->
        @if(count($contentServers) > 1)
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-900 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Select Server:</label>
            <div class="flex flex-wrap gap-2">
                @foreach($contentServers as $index => $server)
                    @if(!empty($server['url']))
                    <button onclick="changeTvShowServer('{{ $server['url'] }}', this)" 
                            class="tv-server-btn px-4 py-2 rounded-lg transition-colors {{ $index === 0 ? 'bg-accent text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:!bg-bg-card-hover dark:!text-text-secondary dark:!hover:bg-bg-card dark:!hover:text-white' }}"
                            style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                        {{ $server['name'] ?? 'Server ' . ($index + 1) }}@if(!empty($server['quality'])) - {{ $server['quality'] }}@endif
                    </button>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- Download Links -->
        @if(!empty($contentDownloadLinks))
        <div class="mt-4 pt-4 border-t border-gray-200 dark:!border-border-secondary">
            <h3 class="text-lg font-bold text-gray-900 mb-3 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Download</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($contentDownloadLinks as $download)
                <a href="{{ $download['url'] }}" 
                   target="_blank" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors"
                   style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    {{ $download['name'] }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif
    @endif

    <!-- Episodes Section -->
    <div class="bg-white border border-gray-200 p-6 dark:!bg-bg-card dark:!border-border-secondary rounded-lg mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Episodes</h2>
        
        @if($episodes && $episodes->count() > 0)
        <div class="space-y-4 max-h-[600px] overflow-y-auto">
            @foreach($episodes as $episode)
            <div class="flex gap-4 p-4 bg-gray-50 dark:!bg-bg-card-hover rounded-lg hover:bg-gray-100 dark:!hover:bg-bg-card transition-colors">
                <!-- Episode Thumbnail -->
                <div class="flex-shrink-0 w-32 h-20 md:w-40 md:h-24 rounded overflow-hidden bg-gray-200 dark:bg-gray-800 relative group">
                    @if($episode->thumbnail_path)
                        <img src="{{ str_starts_with($episode->thumbnail_path, 'http') ? $episode->thumbnail_path : asset('storage/' . $episode->thumbnail_path) }}" 
                             alt="{{ $episode->title }}" 
                             class="w-full h-full object-cover"
                             onerror="this.src='https://via.placeholder.com/400x225?text=No+Image'">
                    @else
                        <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w300') }}" 
                             alt="{{ $episode->title }}" 
                             class="w-full h-full object-cover"
                             onerror="this.src='https://via.placeholder.com/400x225?text=No+Image'">
                    @endif
                    @if($loop->first)
                    <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                
                <!-- Episode Info -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-gray-900 mb-1 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                        {{ $episode->title }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:!text-text-secondary mb-3" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        Eps {{ $episode->episode_number }}@if($episode->air_date) - {{ \Carbon\Carbon::parse($episode->air_date)->format('M d, Y') }}@endif
                    </p>
                    
                    @if($episode->description)
                    <p class="text-xs text-gray-500 dark:!text-text-tertiary mb-3 line-clamp-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        {{ $episode->description }}
                    </p>
                    @endif
                    
                    <!-- Servers -->
                    @if($episode->servers && $episode->servers->count() > 0)
                    @php
                        $episodeServers = $episode->servers->where('is_active', true);
                        $firstEpisodeServer = $episodeServers->first();
                        $episodePlayerUrl = $firstEpisodeServer ? ($firstEpisodeServer->watch_link ?? null) : null;
                        $hasMultipleServers = $episodeServers->count() > 1;
                    @endphp
                    
                    <!-- Embedded Player for Episode (if watch link available) -->
                    @if($episodePlayerUrl)
                    <div class="mt-3 mb-3">
                        <div class="relative w-full bg-black rounded-lg overflow-hidden" style="padding-bottom: 56.25%;">
                            <iframe id="episodePlayer-{{ $episode->id }}" 
                                    src="{{ $episodePlayerUrl }}" 
                                    class="absolute top-0 left-0 w-full h-full border-0" 
                                    allow="autoplay; fullscreen" 
                                    allowfullscreen
                                    frameborder="0">
                            </iframe>
                        </div>
                        
                        <!-- Server Selection for Episode -->
                        @if($hasMultipleServers)
                        <div class="mt-3">
                            <label class="block text-xs font-semibold text-gray-900 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Select Server:</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($episodeServers as $index => $server)
                                    @if($server->watch_link)
                                    <button onclick="changeEpisodeServer('{{ $episode->id }}', '{{ $server->watch_link }}', this)" 
                                            class="episode-server-btn-{{ $episode->id }} px-3 py-1.5 text-xs rounded-lg transition-colors {{ $index === 0 ? 'bg-accent text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:!bg-bg-card-hover dark:!text-text-secondary dark:!hover:bg-bg-card dark:!hover:text-white' }}"
                                            style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                                        {{ $server->server_name }}@if($server->quality) - {{ $server->quality }}@endif
                                    </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Server Links Table -->
                    <div class="mt-3">
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b border-gray-300 dark:!border-border-primary">
                                        <th class="text-left py-2 px-3 text-gray-900 dark:!text-white font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Server</th>
                                        <th class="text-left py-2 px-3 text-gray-900 dark:!text-white font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Quality</th>
                                        <th class="text-left py-2 px-3 text-gray-900 dark:!text-white font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Links</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($episodeServers as $server)
                                    <tr class="border-b border-gray-200 dark:!border-border-secondary hover:bg-gray-100 dark:!hover:bg-bg-card">
                                        <td class="py-2 px-3">
                                            <div class="flex items-center gap-2">
                                                <span class="w-3 h-3 rounded-full border-2 border-red-500 flex items-center justify-center">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                </span>
                                                <span class="text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $server->server_name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-3 text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                            {{ $server->quality ?? '-' }}
                                        </td>
                                        <td class="py-2 px-3">
                                            @if($server->download_link)
                                            <a href="{{ $server->download_link }}" target="_blank" class="text-yellow-600 hover:text-yellow-700 dark:!text-yellow-400 dark:!hover:text-yellow-300 font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Download</a>
                                            @endif
                                            @if($server->watch_link)
                                                @php
                                                    $isTurbovidhls = str_contains($server->watch_link, 'turbovidhls.com');
                                                @endphp
                                                @if($isTurbovidhls)
                                                    <button onclick="openVideoPlayer('{{ $server->watch_link }}', '{{ $server->server_name }}', '{{ $episode->id }}')" class="text-yellow-600 hover:text-yellow-700 dark:!text-yellow-400 dark:!hover:text-yellow-300 font-semibold ml-3" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Watch</button>
                                                @else
                                                    <a href="{{ $server->watch_link }}" target="_blank" class="text-yellow-600 hover:text-yellow-700 dark:!text-yellow-400 dark:!hover:text-yellow-300 font-semibold ml-3" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Watch</a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Video Player Modal/Embed for TurbovidHLS -->
                    <div id="videoPlayerModal-{{ $episode->id }}" class="hidden fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4">
                        <div class="relative w-full max-w-5xl bg-black rounded-lg overflow-hidden">
                            <button onclick="closeVideoPlayer('{{ $episode->id }}')" class="absolute top-4 right-4 z-10 bg-black/70 hover:bg-black/90 text-white rounded-full w-10 h-10 flex items-center justify-center transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <div class="relative w-full" style="padding-bottom: 56.25%;">
                                <iframe id="videoFrame-{{ $episode->id }}" class="absolute top-0 left-0 w-full h-full" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                            </div>
                        </div>
                    </div>
                    @else
                    <p class="text-xs text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">No servers available</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-600 dark:!text-text-secondary text-lg mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                No episodes available yet.
            </p>
            @if(isset($isCustom) && $isCustom && isset($content))
            <p class="text-gray-500 dark:!text-text-tertiary text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Episodes will appear here once they are added and published.
            </p>
            @endif
        </div>
        @endif
    </div>

    <!-- Details Section -->
    <div class="bg-white border border-gray-200 p-6 mb-8 dark:!bg-bg-card dark:!border-border-secondary rounded-lg">
        <h2 class="text-xl font-bold text-gray-900 mb-4 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @if($network)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Network:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $network }}</span>
            </div>
            @endif
            
            @if($releaseDate)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Released:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    {{ \Carbon\Carbon::parse($releaseDate)->format('M d, Y') }}
                    @if($endDate)
                        - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                    @endif
                </span>
            </div>
            @endif
            
            @if($duration)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Duration:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $duration }} min.</span>
            </div>
            @endif
            
            @if($country)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Country:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $country }}</span>
            </div>
            @endif
            
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Type:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $type }}</span>
            </div>
            
            @if($episodeCount)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Episodes:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $episodeCount }}</span>
            </div>
            @endif
            
            @if($director)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Director:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $director }}</span>
            </div>
            @endif
            
            @if(isset($isCustom) && $isCustom && $views > 0)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Views:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ number_format($views) }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Cast Section - Show for both custom and TMDB content -->
    @if(!empty($cast))
    <div class="mb-8">
        <h3 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Cast</h3>
        <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
            @php
                $castList = is_array($cast) ? $cast : [];
            @endphp
            @foreach(array_slice($castList, 0, 10) as $castMember)
                @php
                    $castName = is_array($castMember) ? ($castMember['name'] ?? $castMember) : $castMember;
                    $castCharacter = is_array($castMember) ? ($castMember['character'] ?? '') : '';
                    $profilePath = is_array($castMember) && !empty($castMember['profile_path']) ? $castMember['profile_path'] : null;
                    $profileImageUrl = null;
                    if ($profilePath) {
                        // Check if it's a full URL
                        if (str_starts_with($profilePath, 'http')) {
                            $profileImageUrl = $profilePath;
                        } elseif (str_starts_with($profilePath, '/')) {
                            // TMDB path (starts with /) - use TMDB service
                            $profileImageUrl = app(\App\Services\TmdbService::class)->getImageUrl($profilePath, 'w185');
                        } else {
                            // Custom path - try to use directly or fallback to TMDB service
                            $profileImageUrl = $profilePath;
                        }
                    }
                    
                    // Check if we have cast slug for linking (database casts only)
                    $castSlug = is_array($castMember) ? ($castMember['slug'] ?? null) : null;
                    $castId = is_array($castMember) ? ($castMember['id'] ?? null) : null;
                @endphp
                <div class="min-w-[100px] text-center flex-shrink-0">
                    <a href="{{ ($castSlug || $castId) ? route('cast.show', $castSlug ?? $castId) : '#' }}" class="block {{ !($castSlug || $castId) ? 'cursor-default' : 'hover:opacity-90 transition-opacity' }}">
                        @if($profileImageUrl)
                        <img src="{{ $profileImageUrl }}" 
                             alt="{{ $castName }}" 
                             class="w-20 h-28 md:w-24 md:h-36 object-cover rounded-lg mb-2 shadow-lg mx-auto"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-20 h-28 md:w-24 md:h-36 bg-gray-200 dark:bg-gray-800 rounded-lg mb-2 items-center justify-center hidden mx-auto">
                            <span class="text-gray-400 text-xs">No Photo</span>
                        </div>
                        @else
                        <div class="w-20 h-28 md:w-24 md:h-36 bg-gray-200 dark:bg-gray-800 rounded-lg mb-2 flex items-center justify-center mx-auto">
                            <span class="text-gray-400 text-xs">No Photo</span>
                        </div>
                        @endif
                        <p class="text-sm font-medium text-gray-900 dark:!text-white {{ ($castSlug || $castId) ? 'hover:text-accent transition-colors' : '' }}" style="font-family: 'Poppins', sans-serif; font-weight: 600;">{{ $castName }}</p>
                        @if($castCharacter)
                        <p class="text-xs text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            {{ $castCharacter }}
                        </p>
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(!isset($isCustom) || !$isCustom)
        @if(isset($tvShow['videos']['results']) && count($tvShow['videos']['results']) > 0)
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Trailers</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach(array_slice($tvShow['videos']['results'], 0, 3) as $video)
                @if($video['site'] === 'YouTube')
                <div class="relative pb-[56.25%] h-0 overflow-hidden rounded-xl">
                    <iframe src="https://www.youtube.com/embed/{{ $video['key'] }}" 
                            class="absolute top-0 left-0 w-full h-full border-0"
                            allowfullscreen></iframe>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif

    @endif

    <!-- Similar TV Shows -->
    <x-recommendations :items="$similarTvShows ?? []" title="Similar TV Shows" routeName="tv-shows.show" />

    <!-- You May Also Like -->
    <x-recommendations :items="$youMayAlsoLike ?? []" title="You May Also Like" routeName="tv-shows.show" />

    <!-- Trending TV Shows -->
    <x-recommendations :items="$trendingTvShows ?? []" title="Trending TV Shows" routeName="tv-shows.show" />

    <!-- Trending Movies -->
    <x-recommendations :items="$trendingMovies ?? []" title="Trending Movies" routeName="movies.show" />

    <!-- Recommended Movies Section (Legacy) -->
    @if(isset($recommendedMovies) && $recommendedMovies->count() > 0)
    <div class="mt-12 mb-12">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:!text-white mb-6 pl-4 border-l-4 border-accent" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Recommended Movies
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-5 gap-3 sm:gap-4 md:gap-5 lg:gap-6">
            @foreach($recommendedMovies->take(10) as $recommended)
            @php
                // Handle Content model instances
                $movieId = $recommended->slug ?? ('custom_' . $recommended->id);
                $movieTitle = $recommended->title ?? 'Unknown';
                $posterPath = $recommended->poster_path;
                $releaseDate = $recommended->release_date ? $recommended->release_date->format('Y-m-d') : null;
                $rating = $recommended->rating ?? 0;
                
                // Get image URL
                $imageUrl = null;
                if ($posterPath) {
                    if (str_starts_with($posterPath, 'http')) {
                        $imageUrl = $posterPath;
                    } else {
                        $contentType = $recommended->content_type ?? 'custom';
                        if (in_array($contentType, ['tmdb', 'article']) || str_starts_with($posterPath, '/')) {
                            $imageUrl = app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w342');
                        } else {
                            $imageUrl = $posterPath;
                        }
                    }
                }
            @endphp
            <a href="{{ route('movies.show', $movieId) }}" 
               class="group relative bg-white dark:!bg-bg-card rounded-xl overflow-hidden border border-gray-200 dark:!border-border-primary hover:border-accent/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-accent/20 cursor-pointer">
                <!-- Image Container -->
                <div class="relative overflow-hidden aspect-[2/3] bg-gradient-to-br from-gray-100 to-gray-200 dark:!from-bg-card dark:!to-bg-card-hover">
                    <img src="{{ $imageUrl ?? 'https://via.placeholder.com/300x450?text=No+Image' }}" 
                         alt="{{ $movieTitle }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                         onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <!-- Rating Badge -->
                    <div class="absolute top-2 right-2 bg-black/80 dark:!bg-black/90 backdrop-blur-sm rounded-full px-2 py-1 flex items-center gap-1 border border-accent/30">
                        <span class="text-yellow-500 text-xs">★</span>
                        <span class="text-white text-xs font-bold">{{ number_format($rating, 1) }}</span>
                    </div>
                    
                    <!-- Hover Overlay with Info -->
                    <div class="absolute inset-0 flex items-end opacity-0 group-hover:opacity-100 transition-all duration-500 pb-3 px-3">
                        <div class="w-full">
                            <div class="bg-accent/90 backdrop-blur-sm rounded-lg px-3 py-2 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                                <p class="text-white text-xs font-semibold text-center" style="font-family: 'Poppins', sans-serif; font-weight: 600;">View Details</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card Content -->
                <div class="p-3 md:p-4 bg-white dark:!bg-bg-card border-t border-gray-100 dark:!border-border-secondary">
                    <h3 class="text-sm md:text-base font-bold text-gray-900 dark:!text-white mb-2 line-clamp-2 group-hover:text-accent transition-colors duration-300 leading-tight" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                        {{ $movieTitle }}
                    </h3>
                    <div class="flex items-center justify-between">
                        @if($releaseDate)
                        <span class="text-gray-600 dark:!text-text-secondary text-xs md:text-sm font-medium" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            {{ \Carbon\Carbon::parse($releaseDate)->format('Y') }}
                        </span>
                        @else
                        <span class="text-gray-600 dark:!text-text-secondary text-xs md:text-sm font-medium" style="font-family: 'Poppins', sans-serif; font-weight: 400;">N/A</span>
                        @endif
                        <div class="flex items-center gap-1.5 bg-gray-100 dark:!bg-bg-card-hover rounded-full px-2 py-1">
                            <span class="text-yellow-500 text-xs">★</span>
                            <span class="font-bold text-gray-900 dark:!text-white text-xs" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($rating, 1) }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    /* Article Content Styling */
    .article-content {
        font-family: 'Poppins', sans-serif;
        line-height: 1.8;
        color: #374151;
    }

    .dark .article-content {
        color: #e5e7eb;
    }

    .article-content article {
        width: 100%;
    }

    .article-content h2 {
        font-size: 2rem;
        font-weight: 800;
        color: #111827;
        margin-top: 2rem;
        margin-bottom: 1.5rem;
        line-height: 1.3;
        font-family: 'Poppins', sans-serif;
    }

    .dark .article-content h2 {
        color: #ffffff;
    }

    .article-content h2:first-child {
        margin-top: 0;
    }

    .article-content h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-top: 2rem;
        margin-bottom: 1rem;
        line-height: 1.4;
        font-family: 'Poppins', sans-serif;
    }

    .dark .article-content h3 {
        color: #f3f4f6;
    }

    .article-content p {
        font-size: 1.125rem;
        line-height: 1.8;
        margin-bottom: 1.5rem;
        color: #4b5563;
        font-weight: 400;
        font-family: 'Poppins', sans-serif;
    }

    .dark .article-content p {
        color: #d1d5db;
    }

    .article-content strong {
        font-weight: 700;
        color: #111827;
    }

    .dark .article-content strong {
        color: #ffffff;
    }

    .article-content ul,
    .article-content ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
        color: #4b5563;
    }

    .dark .article-content ul,
    .dark .article-content ol {
        color: #d1d5db;
    }

    .article-content li {
        margin-bottom: 0.75rem;
        line-height: 1.8;
        font-size: 1.125rem;
    }

    .article-content a {
        color: #dc2626;
        text-decoration: underline;
        font-weight: 500;
        transition: color 0.2s;
    }

    .article-content a:hover {
        color: #b91c1c;
    }

    .dark .article-content a {
        color: #f87171;
    }

    .dark .article-content a:hover {
        color: #ef4444;
    }

    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 2rem 0;
    }

    .article-content blockquote {
        border-left: 4px solid #dc2626;
        padding-left: 1.5rem;
        margin: 2rem 0;
        font-style: italic;
        color: #6b7280;
    }

    .dark .article-content blockquote {
        border-left-color: #f87171;
        color: #9ca3af;
    }
</style>

<script>
    function openVideoPlayer(videoUrl, serverName, episodeId) {
        const modal = document.getElementById('videoPlayerModal-' + episodeId);
        const iframe = document.getElementById('videoFrame-' + episodeId);
        
        if (modal && iframe) {
            // Set the iframe source to the video URL
            iframe.src = videoUrl;
            // Show the modal
            modal.classList.remove('hidden');
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }
    }

    function closeVideoPlayer(episodeId) {
        const modal = document.getElementById('videoPlayerModal-' + episodeId);
        const iframe = document.getElementById('videoFrame-' + episodeId);
        
        if (modal && iframe) {
            // Hide the modal
            modal.classList.add('hidden');
            // Clear the iframe source to stop video playback
            iframe.src = '';
            // Restore body scroll
            document.body.style.overflow = '';
        }
    }

    // Close modal when clicking outside the video player
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('bg-black/90')) {
            const modals = document.querySelectorAll('[id^="videoPlayerModal-"]');
            modals.forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    const episodeId = modal.id.replace('videoPlayerModal-', '');
                    closeVideoPlayer(episodeId);
                }
            });
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('[id^="videoPlayerModal-"]');
            modals.forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    const episodeId = modal.id.replace('videoPlayerModal-', '');
                    closeVideoPlayer(episodeId);
                }
            });
        }
    });

    // Change TV Show server (for "Watch All Episodes" player)
    function changeTvShowServer(videoUrl, buttonElement) {
        const iframe = document.getElementById('tvShowPlayer');
        if (iframe) {
            iframe.src = videoUrl;
            
            // Update active button state
            const allButtons = document.querySelectorAll('.tv-server-btn');
            allButtons.forEach(btn => {
                btn.classList.remove('bg-accent', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700', 'dark:!bg-bg-card-hover', 'dark:!text-text-secondary');
            });
            
            if (buttonElement) {
                buttonElement.classList.remove('bg-gray-200', 'text-gray-700', 'dark:!bg-bg-card-hover', 'dark:!text-text-secondary');
                buttonElement.classList.add('bg-accent', 'text-white');
            }
        }
    }

    // Change episode server (for individual episode players)
    function changeEpisodeServer(episodeId, videoUrl, buttonElement) {
        const iframe = document.getElementById('episodePlayer-' + episodeId);
        if (iframe) {
            iframe.src = videoUrl;
            
            // Update active button state for this episode
            const allButtons = document.querySelectorAll('.episode-server-btn-' + episodeId);
            allButtons.forEach(btn => {
                btn.classList.remove('bg-accent', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700', 'dark:!bg-bg-card-hover', 'dark:!text-text-secondary');
            });
            
            if (buttonElement) {
                buttonElement.classList.remove('bg-gray-200', 'text-gray-700', 'dark:!bg-bg-card-hover', 'dark:!text-text-secondary');
                buttonElement.classList.add('bg-accent', 'text-white');
            }
        }
    }
</script>

    <!-- Comments Section -->
    @if(isset($isCustom) && $isCustom && isset($content))
        <x-comments :contentId="$content->id" />
    @elseif(isset($content) && $content)
        <x-comments :contentId="$content->id" />
    @endif
@endsection
