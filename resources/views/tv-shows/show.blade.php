@extends('layouts.app')

@section('title', (isset($isCustom) && $isCustom ? ($content->title ?? 'TV Show') : ($tvShow['name'] ?? 'TV Show')) . ' - Nazaarabox')

@section('content')
@php
    if (isset($isCustom) && $isCustom) {
        $title = $content->title;
        $originalTitle = $content->title;
        $rating = $content->rating ?? 0;
        $status = $content->series_status ?? 'ongoing';
        $network = $content->network;
        $releaseDate = $content->release_date;
        $endDate = $content->end_date;
        $duration = $content->duration;
        $country = $content->country;
        $type = ucfirst(str_replace('_', ' ', $content->type));
        $episodeCount = $content->episode_count ?? $content->episodes()->count();
        $director = $content->director;
        $genres = $content->genres ?? [];
        $cast = $content->cast ?? [];
        $description = $content->description;
        $posterPath = $content->poster_path;
        $episodes = $content->episodes;
        $currentEpisodes = $episodes->count();
    } else {
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
        $episodes = $content->episodes ?? collect([]);
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

<div class="w-full px-4 sm:px-6 lg:px-8 xl:px-12 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row gap-6 items-start">
            <!-- Poster -->
            <div class="flex-shrink-0">
                <div class="w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-800 border-4 border-white dark:border-gray-700 shadow-lg">
                    @if(isset($isCustom) && $isCustom)
                        <img src="{{ $posterPath ? (str_starts_with($posterPath, 'http') ? $posterPath : asset('storage/' . $posterPath)) : 'https://via.placeholder.com/200x200?text=No+Image' }}" 
                             alt="{{ $title }}" 
                             class="w-full h-full object-cover"
                             onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                    @else
                        <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w500') }}" 
                             alt="{{ $title }}" 
                             class="w-full h-full object-cover"
                             onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                    @endif
                </div>
            </div>
            
            <!-- Title and Status -->
            <div class="flex-1">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    {{ $title }}
                </h1>
                @if($originalTitle !== $title)
                <p class="text-lg text-gray-600 mb-3 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    {{ $originalTitle }}
                </p>
                @endif
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-yellow-500 text-xl">★</span>
                        <span class="text-lg font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Rating {{ number_format($rating, 1) }}</span>
                    </div>
                </div>
                <p class="text-yellow-500 font-semibold text-lg dark:!text-yellow-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Status: {{ $statusDisplay }}
                </p>
            </div>
        </div>
    </div>

    <!-- Details Section -->
    <div class="bg-white border border-gray-200 p-6 mb-8 dark:!bg-bg-card dark:!border-border-secondary">
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
            
            @if(!empty($cast))
            <div class="md:col-span-2">
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Casts:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    @if(isset($isCustom) && $isCustom)
                        {{ is_array($cast) ? implode(', ', array_slice($cast, 0, 10)) : $cast }}
                    @else
                        {{ implode(', ', array_slice(array_column($cast, 'name'), 0, 10)) }}
                    @endif
                </span>
            </div>
            @endif
        </div>
        
        @if(!empty($genres))
        <div class="mt-4">
            <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Genres:</span>
            <div class="flex flex-wrap gap-2 mt-2">
                @if(isset($isCustom) && $isCustom)
                    @foreach(is_array($genres) ? $genres : [] as $genre)
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs dark:!bg-bg-card-hover dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ is_array($genre) ? ($genre['name'] ?? $genre) : $genre }}</span>
                    @endforeach
                @else
                    @foreach($genres as $genre)
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs dark:!bg-bg-card-hover dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $genre['name'] }}</span>
                    @endforeach
                @endif
            </div>
        </div>
        @endif
        
        @if($description)
        <div class="mt-4">
            <p class="text-gray-600 dark:!text-text-secondary leading-relaxed" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $description }}</p>
        </div>
        @endif
    </div>

    <!-- Episodes Section -->
    <div class="bg-white border border-gray-200 p-6 dark:!bg-bg-card dark:!border-border-secondary">
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
                                    @foreach($episode->servers->where('is_active', true) as $server)
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
                                            <a href="{{ $server->watch_link }}" target="_blank" class="text-yellow-600 hover:text-yellow-700 dark:!text-yellow-400 dark:!hover:text-yellow-300 font-semibold ml-3" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Watch</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
        <p class="text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">No episodes available yet.</p>
        @endif
    </div>

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

        @if(isset($tvShow['recommendations']['results']) && count($tvShow['recommendations']['results']) > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Recommended TV Shows</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-5 gap-4">
                @foreach(array_slice($tvShow['recommendations']['results'], 0, 10) as $recommended)
                <a href="{{ route('tv-shows.show', $recommended['id']) }}" 
                   class="group relative bg-white overflow-hidden cursor-pointer dark:!bg-bg-card transition-all duration-300">
                    <div class="relative overflow-hidden w-full aspect-video bg-gray-200 dark:bg-gray-800">
                        <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($recommended['backdrop_path'] ?? $recommended['poster_path'] ?? null, 'w342') }}" 
                             alt="{{ $recommended['name'] ?? 'TV Show' }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                             onerror="this.src='https://via.placeholder.com/342x513?text=No+Image'">
                    </div>
                    <div class="p-2 bg-white dark:!bg-bg-card">
                        <h3 class="text-sm font-bold text-gray-900 line-clamp-2 group-hover:text-accent transition-colors dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                            {{ $recommended['name'] ?? 'Unknown' }}
                        </h3>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
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
</style>
@endsection
