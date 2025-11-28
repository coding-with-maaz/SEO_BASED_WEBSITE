@extends('layouts.app')

@section('title', 'Home - Nazaarabox')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 xl:px-12 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Area (2 columns on large screens) -->
        <div class="lg:col-span-2">
            @php
                $allContent = [];
                
                // Combine movies and TV shows
                if (!empty($popularMovies)) {
                    foreach (array_slice($popularMovies, 0, 20) as $movie) {
                        $allContent[] = [
                            'type' => 'movie',
                            'id' => $movie['id'],
                            'title' => $movie['title'] ?? 'Unknown',
                            'date' => $movie['release_date'] ?? null,
                            'rating' => $movie['vote_average'] ?? 0,
                            'backdrop' => $movie['backdrop_path'] ?? $movie['poster_path'] ?? null,
                            'poster' => $movie['poster_path'] ?? null,
                            'overview' => $movie['overview'] ?? '',
                        ];
                    }
                }
                
                if (!empty($popularTvShows)) {
                    foreach (array_slice($popularTvShows, 0, 20) as $tvShow) {
                        $allContent[] = [
                            'type' => 'tv',
                            'id' => $tvShow['id'],
                            'title' => $tvShow['name'] ?? 'Unknown',
                            'date' => $tvShow['first_air_date'] ?? null,
                            'rating' => $tvShow['vote_average'] ?? 0,
                            'backdrop' => $tvShow['backdrop_path'] ?? $tvShow['poster_path'] ?? null,
                            'poster' => $tvShow['poster_path'] ?? null,
                            'overview' => $tvShow['overview'] ?? '',
                        ];
                    }
                }
                
                // Sort by date (newest first)
                usort($allContent, function($a, $b) {
                    $dateA = $a['date'] ?? '1970-01-01';
                    $dateB = $b['date'] ?? '1970-01-01';
                    return strcmp($dateB, $dateA);
                });
            @endphp

            <!-- 2 Column Grid for Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach(array_slice($allContent, 0, 20) as $item)
                <article class="group relative bg-white overflow-hidden cursor-pointer dark:!bg-bg-card transition-all duration-300">
                    <a href="{{ $item['type'] === 'movie' ? route('movies.show', $item['id']) : route('tv-shows.show', $item['id']) }}" class="block">
                        <!-- Full Image - Backdrop Image with 16:9 Aspect Ratio -->
                        <div class="relative overflow-hidden w-full aspect-video bg-gray-200 dark:bg-gray-800">
                            <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($item['backdrop'] ?? $item['poster'], 'w780') }}" 
                                 alt="{{ $item['title'] }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                 onerror="this.src='https://via.placeholder.com/780x439?text=No+Image'">
                        </div>
                        
                        <!-- Card Content -->
                        <div class="p-3 bg-white dark:!bg-bg-card">
                            <!-- Title - Bold Text, Always Visible -->
                            <h2 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-accent transition-colors duration-300 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700; line-height: 1.4;">
                                {{ $item['title'] }}
                                @if($item['type'] === 'movie')
                                    <span class="font-normal text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">(Movie)</span>
                                @else
                                    <span class="font-normal text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">(TV Show)</span>
                                @endif
                            </h2>
                            
                            <!-- Content Details - Like "Hindi Dubbed [ Episode 6 ADD ]" -->
                            <p class="text-gray-600 text-xs mb-1 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400; line-height: 1.4;">
                                @if($item['type'] === 'movie')
                                    Movie - [ Full Movie ]
                                @else
                                    TV Series - [ Episode 1 ADD ]
                                @endif
                            </p>
                            
                            <!-- Date - Smaller Lighter Gray Text -->
                            @if($item['date'])
                            <p class="text-gray-500 text-xs dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                {{ \Carbon\Carbon::parse($item['date'])->format('F d, Y') }}
                            </p>
                            @endif
                        </div>
                    </a>
                </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center items-center gap-2 flex-wrap">
                <a href="#" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    Previous
                </a>
                <a href="#" class="px-4 py-2 bg-accent text-white font-semibold transition-all hover:bg-accent-light" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    1
                </a>
                <a href="#" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    2
                </a>
                <a href="#" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    3
                </a>
                <span class="text-gray-600 px-2 dark:!text-text-secondary">…</span>
                <a href="#" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    74
                </a>
                <a href="#" class="px-4 py-2 bg-accent hover:bg-accent-light text-white font-semibold transition-all" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    NEXT >
                </a>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="lg:col-span-1">
            <!-- Telegram Promotion Card -->
            <div class="bg-white border border-gray-200 p-6 mb-6 sticky top-24 dark:!bg-bg-card dark:!border-border-secondary">
                <h3 class="text-lg font-bold text-gray-900 mb-4 text-center dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Join our Telegram Channel & Group</h3>
                <div class="flex flex-col items-center justify-center space-y-4">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c.172 0 .31.139.31.311v1.378c0 .172-.138.311-.31.311h-1.378c-.172 0-.311-.139-.311-.311V8.472c0-.172.139-.311.311-.311h1.378zm-3.378 0c.172 0 .311.139.311.311v1.378c0 .172-.139.311-.311.311H12.81c-.172 0-.311-.139-.311-.311V8.472c0-.172.139-.311.311-.311h1.374zm-3.378 0c.172 0 .311.139.311.311v1.378c0 .172-.139.311-.311.311H9.432c-.172 0-.311-.139-.311-.311V8.472c0-.172.139-.311.311-.311h1.374zm-3.378 0c.172 0 .311.139.311.311v1.378c0 .172-.139.311-.311.311H6.054c-.172 0-.311-.139-.311-.311V8.472c0-.172.139-.311.311-.311h1.374zm12.756 2.322H5.184c-.172 0-.311.139-.311.311v1.378c0 .172.139.311.311.311h13.188c.172 0 .311-.139.311-.311v-1.378c0-.172-.139-.311-.311-.311z"/>
                        </svg>
                    </div>
                    <p class="text-gray-900 font-semibold text-lg dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Telegram</p>
                    <a href="#" class="w-full px-6 py-3 bg-gradient-primary hover:bg-accent-light text-white font-semibold rounded-lg transition-all text-center" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Join Channel
                    </a>
                </div>
            </div>

            <!-- Popular Section -->
            <div class="bg-white border border-gray-200 p-6 dark:!bg-bg-card dark:!border-border-secondary">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-200 pb-3 dark:!text-white dark:!border-border-primary" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Popular Now</h3>
                <div class="space-y-4">
                    @if(!empty($topRatedMovies))
                        @foreach(array_slice($topRatedMovies, 0, 5) as $movie)
                        <a href="{{ route('movies.show', $movie['id']) }}" class="flex gap-3 group hover:bg-gray-50 p-2 rounded-lg transition-all dark:!hover:bg-bg-card-hover">
                            <div class="flex-shrink-0 w-16 h-24 rounded overflow-hidden bg-gray-100 dark:!bg-bg-card-hover">
                                <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($movie['poster_path'] ?? null, 'w185') }}" 
                                     alt="{{ $movie['title'] }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                     onerror="this.src='https://via.placeholder.com/185x278?text=No+Image'">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 group-hover:text-accent transition-colors line-clamp-2 mb-1 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600; line-height: 1.4;">
                                    {{ $movie['title'] ?? 'Unknown' }}
                                </h4>
                                <p class="text-gray-600 text-xs mb-1 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                    {{ \Carbon\Carbon::parse($movie['release_date'] ?? '')->format('Y') ?? 'N/A' }}
                                </p>
                                <div class="flex items-center gap-1">
                                    <span class="text-rating text-xs">★</span>
                                    <span class="text-gray-900 text-xs font-semibold dark:!text-white">{{ number_format($movie['vote_average'] ?? 0, 1) }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
