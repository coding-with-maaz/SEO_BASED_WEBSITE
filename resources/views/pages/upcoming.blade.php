@extends('layouts.app')

@section('title', 'Upcoming Movies & TV Shows - Nazaarabox')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 xl:px-12 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Area (2 columns on large screens) -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Upcoming Movies & TV Shows
            </h2>

            @php
                $allUpcoming = [];
                
                // Add custom upcoming content
                if (!empty($customUpcoming)) {
                    foreach ($customUpcoming as $content) {
                        $allUpcoming[] = [
                            'id' => $content->slug ?? ('custom_' . $content->id),
                            'slug' => $content->slug,
                            'name' => $content->title,
                            'title' => $content->title,
                            'release_date' => $content->release_date ? $content->release_date->format('Y-m-d') : null,
                            'first_air_date' => $content->release_date ? $content->release_date->format('Y-m-d') : null,
                            'backdrop_path' => $content->backdrop_path,
                            'poster_path' => $content->poster_path,
                            'is_custom' => true,
                            'content_id' => $content->id,
                            'content_type' => $content->content_type ?? 'custom',
                            'dubbing_language' => $content->dubbing_language,
                            'type' => $content->type,
                            'rating' => $content->rating ?? 0,
                            'is_movie' => in_array($content->type, ['movie', 'documentary', 'short_film']),
                        ];
                    }
                }
                
                // Add TMDB upcoming movies
                if (!empty($upcomingMovies)) {
                    foreach ($upcomingMovies as $movie) {
                        $allUpcoming[] = [
                            'id' => $movie['id'],
                            'name' => $movie['title'] ?? 'Unknown',
                            'title' => $movie['title'] ?? 'Unknown',
                            'release_date' => $movie['release_date'] ?? null,
                            'backdrop_path' => $movie['backdrop_path'] ?? null,
                            'poster_path' => $movie['poster_path'] ?? null,
                            'is_custom' => false,
                            'type' => 'movie',
                            'rating' => $movie['vote_average'] ?? 0,
                            'is_movie' => true,
                        ];
                    }
                }
                
                // Sort by release date (soonest first)
                usort($allUpcoming, function($a, $b) {
                    $dateA = $a['release_date'] ?? $a['first_air_date'] ?? '9999-12-31';
                    $dateB = $b['release_date'] ?? $b['first_air_date'] ?? '9999-12-31';
                    return strcmp($dateA, $dateB);
                });
            @endphp

            @if(!empty($allUpcoming))
            <!-- 2 Column Grid for Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($allUpcoming as $item)
                <article class="group relative bg-white overflow-hidden cursor-pointer dark:!bg-bg-card transition-all duration-300">
                    <a href="{{ $item['is_movie'] ? route('movies.show', $item['id']) : route('tv-shows.show', $item['id']) }}" class="block">
                        <!-- Full Image - Backdrop Image with 16:9 Aspect Ratio -->
                        <div class="relative overflow-hidden w-full aspect-video bg-gray-200 dark:bg-gray-800">
                            @if($item['is_custom'] ?? false)
                                @php
                                    $imageUrl = null;
                                    $backdropPath = !empty($item['backdrop_path']) ? $item['backdrop_path'] : null;
                                    $posterPath = !empty($item['poster_path']) ? $item['poster_path'] : null;
                                    $imagePath = $backdropPath ?? $posterPath;
                                    
                                    if ($imagePath) {
                                        // Use same logic as edit page
                                        if (str_starts_with($imagePath, 'http')) {
                                            // Full URL - use directly
                                            $imageUrl = $imagePath;
                                        } elseif (($item['content_type'] ?? 'custom') === 'tmdb') {
                                            // TMDB content - use TMDB service
                                            $imageUrl = app(\App\Services\TmdbService::class)->getImageUrl($imagePath, 'w780');
                                        } else {
                                            // Custom content - use URL/path directly from database
                                            $imageUrl = $imagePath;
                                        }
                                    }
                                @endphp
                                <img src="{{ $imageUrl ?? 'https://via.placeholder.com/780x439?text=No+Image' }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                     onerror="this.src='https://via.placeholder.com/780x439?text=No+Image'">
                            @else
                                <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($item['backdrop_path'] ?? $item['poster_path'] ?? null, 'w780') }}" 
                                     alt="{{ $item['name'] ?? ($item['is_movie'] ? 'Movie' : 'TV Show') }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                     onerror="this.src='https://via.placeholder.com/780x439?text=No+Image'">
                            @endif
                            
                            <!-- Upcoming Badge -->
                            <div class="absolute top-2 left-2 bg-accent text-white px-3 py-1 rounded-full text-xs font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Upcoming
                            </div>
                        </div>
                        
                        <!-- Card Content -->
                        <div class="p-3 bg-white dark:!bg-bg-card">
                            <!-- Title - Bold Text -->
                            <h2 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-accent transition-colors duration-300 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700; line-height: 1.4;">
                                {{ $item['name'] ?? 'Unknown' }}
                                @php
                                    if ($item['is_movie'] ?? false) {
                                        $typeLabel = 'Movie';
                                    } else {
                                        $typeLabel = ucfirst(str_replace('_', ' ', $item['type'] ?? 'TV Show'));
                                    }
                                @endphp
                                <span class="font-normal text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">({{ $typeLabel }})</span>
                            </h2>
                            
                            <!-- Content Details -->
                            <p class="text-gray-600 text-xs mb-1 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400; line-height: 1.4;">
                                {{ $typeLabel }}
                                @if($item['dubbing_language'] ?? null)
                                    - {{ ucfirst($item['dubbing_language']) }} Dubbed
                                @endif
                                @if($item['release_date'] ?? $item['first_air_date'] ?? null)
                                    @php
                                        $releaseDate = $item['release_date'] ?? $item['first_air_date'];
                                        $date = \Carbon\Carbon::parse($releaseDate);
                                    @endphp
                                    • {{ $date->format('Y') }}
                                    @if($item['rating'] ?? null && $item['rating'] > 0)
                                        • ★ {{ number_format($item['rating'], 1) }}
                                    @endif
                                @endif
                            </p>
                            
                            <!-- Release Date -->
                            @if($item['release_date'] ?? $item['first_air_date'] ?? null)
                            @php
                                $releaseDate = $item['release_date'] ?? $item['first_air_date'];
                                $date = \Carbon\Carbon::parse($releaseDate);
                            @endphp
                            <p class="text-gray-500 text-xs dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                Release Date: {{ $date->format('M d, Y') }}
                                @if($date->isFuture())
                                    <span class="text-accent font-semibold">({{ $date->diffForHumans() }})</span>
                                @endif
                            </p>
                            @endif
                        </div>
                    </a>
                </article>
                @endforeach
            </div>
            
            @if(isset($totalPages) && $totalPages > 1)
            <div class="mt-8 flex justify-center items-center gap-2 flex-wrap">
                @if($currentPage > 1)
                <a href="{{ route('upcoming', ['page' => $currentPage - 1]) }}" 
                   class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    Previous
                </a>
                @endif
                
                @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                <a href="{{ route('upcoming', ['page' => $i]) }}" 
                   class="px-4 py-2 transition-all {{ $i === $currentPage ? 'bg-accent text-white dark:!bg-accent dark:!text-white' : 'bg-white hover:bg-gray-50 text-gray-900 dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white' }}" style="font-family: 'Poppins', sans-serif; font-weight: {{ $i === $currentPage ? '600' : '500' }};">
                    {{ $i }}
                </a>
                @endfor
                
                @if($currentPage < $totalPages)
                <a href="{{ route('upcoming', ['page' => $currentPage + 1]) }}" 
                   class="px-4 py-2 bg-accent hover:bg-accent-light text-white font-semibold transition-all" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    NEXT >
                </a>
                @endif
            </div>
            @endif
            
            @else
            <div class="text-center py-16">
                <p class="text-gray-600 dark:!text-text-secondary text-lg md:text-xl" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    No upcoming movies or TV shows available at the moment.
                </p>
            </div>
            @endif
        </div>

        <!-- Right Sidebar -->
        <div class="lg:col-span-1">
            <!-- Download Our App Card -->
            <div class="bg-white border border-gray-200 p-6 mb-6 sticky top-24 dark:!bg-bg-card dark:!border-border-secondary">
                <h3 class="text-lg font-bold text-gray-900 mb-4 text-center dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Download our app</h3>
                <div class="flex flex-col items-center justify-center space-y-3">
                    <a href="https://play.google.com/store/apps/details?id=com.pro.name.generator" target="_blank" rel="noopener noreferrer" class="w-full px-4 py-3 bg-gradient-primary hover:bg-accent-light text-white font-semibold rounded-lg transition-all text-center flex items-center justify-center gap-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                        </svg>
                        Nazaarabox App
                    </a>
                    <a href="https://play.google.com/store/apps/details?id=com.maazkhan07.jobsinquwait" target="_blank" rel="noopener noreferrer" class="w-full px-4 py-3 bg-gradient-primary hover:bg-accent-light text-white font-semibold rounded-lg transition-all text-center flex items-center justify-center gap-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                        </svg>
                        ASIAN2DAY App
                    </a>
                </div>
            </div>

            <!-- Popular Now Section -->
            <div class="bg-white border border-gray-200 p-6 dark:!bg-bg-card dark:!border-border-secondary">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-200 pb-3 dark:!text-white dark:!border-border-primary" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Popular Now</h3>
                <div class="space-y-4">
                    @if(!empty($popularContent))
                        @foreach($popularContent as $item)
                        @php
                            $routeName = in_array($item->type, ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']) ? 'tv-shows.show' : 'movies.show';
                            $itemId = $item->slug ?? ('custom_' . $item->id);
                            $posterPath = $item->poster_path;
                            $imageUrl = null;
                            
                            if ($posterPath) {
                                // Use same logic as edit page
                                if (str_starts_with($posterPath, 'http')) {
                                    // Full URL - use directly
                                    $imageUrl = $posterPath;
                                } elseif (($item->content_type ?? 'custom') === 'tmdb') {
                                    // TMDB content - use TMDB service
                                    $imageUrl = app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w185');
                                } else {
                                    // Custom content - use URL/path directly from database
                                    $imageUrl = $posterPath;
                                }
                            }
                        @endphp
                        <a href="{{ route($routeName, $itemId) }}" class="flex gap-3 group hover:bg-gray-50 p-2 rounded-lg transition-all dark:!hover:bg-bg-card-hover">
                            <div class="flex-shrink-0 w-16 h-24 rounded overflow-hidden bg-gray-100 dark:!bg-bg-card-hover">
                                <img src="{{ $imageUrl ?? 'https://via.placeholder.com/185x278?text=No+Image' }}" 
                                     alt="{{ $item->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                     onerror="this.src='https://via.placeholder.com/185x278?text=No+Image'">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 group-hover:text-accent transition-colors line-clamp-2 mb-1 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600; line-height: 1.4;">
                                    {{ $item->title ?? 'Unknown' }}
                                </h4>
                                <p class="text-gray-600 text-xs mb-1 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                    {{ $item->release_date ? $item->release_date->format('Y') : 'N/A' }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600 text-xs dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                        👁 {{ number_format($item->views ?? 0) }} views
                                    </span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    @else
                        <p class="text-gray-600 text-sm dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">No popular content available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

