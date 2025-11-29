@extends('layouts.app')

@section('title', 'Movies - Nazaarabox')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 xl:px-12 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Area (2 columns on large screens) -->
        <div class="lg:col-span-2">
            <!-- Filter Tabs -->
            <div class="flex flex-wrap gap-3 mb-8">
                <a href="{{ route('movies.index', ['type' => 'popular']) }}" 
                   class="px-4 py-2 border border-gray-300 transition-all {{ $type === 'popular' ? 'bg-accent text-white border-accent' : 'bg-white hover:bg-gray-50 text-gray-900 dark:!bg-bg-card dark:!border-border-primary dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white' }}" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    Popular
                </a>
                <a href="{{ route('movies.index', ['type' => 'top_rated']) }}" 
                   class="px-4 py-2 border border-gray-300 transition-all {{ $type === 'top_rated' ? 'bg-accent text-white border-accent' : 'bg-white hover:bg-gray-50 text-gray-900 dark:!bg-bg-card dark:!border-border-primary dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white' }}" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    Top Rated
                </a>
                <a href="{{ route('movies.index', ['type' => 'now_playing']) }}" 
                   class="px-4 py-2 border border-gray-300 transition-all {{ $type === 'now_playing' ? 'bg-accent text-white border-accent' : 'bg-white hover:bg-gray-50 text-gray-900 dark:!bg-bg-card dark:!border-border-primary dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white' }}" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    Now Playing
                </a>
                <a href="{{ route('movies.index', ['type' => 'upcoming']) }}" 
                   class="px-4 py-2 border border-gray-300 transition-all {{ $type === 'upcoming' ? 'bg-accent text-white border-accent' : 'bg-white hover:bg-gray-50 text-gray-900 dark:!bg-bg-card dark:!border-border-primary dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white' }}" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    Upcoming
                </a>
            </div>

            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Movies
            </h2>

    @php
        $allMovies = [];
        
        // Add custom movies first (higher priority)
        if (!empty($customMovies)) {
            foreach ($customMovies as $content) {
                $allMovies[] = [
                    'id' => $content->slug ?? ('custom_' . $content->id), // Use slug if available
                    'slug' => $content->slug,
                    'title' => $content->title,
                    'release_date' => $content->release_date ? $content->release_date->format('Y-m-d') : null,
                    'backdrop_path' => $content->backdrop_path,
                    'poster_path' => $content->poster_path,
                    'is_custom' => true,
                    'content_id' => $content->id,
                    'dubbing_language' => $content->dubbing_language,
                    'type' => $content->type,
                ];
            }
        }
        
        // Add TMDB movies
        if (!empty($movies)) {
            foreach ($movies as $movie) {
                $allMovies[] = [
                    'id' => $movie['id'],
                    'title' => $movie['title'] ?? 'Unknown',
                    'release_date' => $movie['release_date'] ?? null,
                    'backdrop_path' => $movie['backdrop_path'] ?? null,
                    'poster_path' => $movie['poster_path'] ?? null,
                    'is_custom' => false,
                ];
            }
        }
        
        // Sort by date (newest first), custom content first if same date
        usort($allMovies, function($a, $b) {
            $dateA = $a['release_date'] ?? '1970-01-01';
            $dateB = $b['release_date'] ?? '1970-01-01';
            if ($dateA === $dateB) {
                return ($b['is_custom'] ?? false) <=> ($a['is_custom'] ?? false);
            }
            return strcmp($dateB, $dateA);
        });
    @endphp

            @if(!empty($allMovies))
            <!-- 2 Column Grid for Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($allMovies as $movie)
        <article class="group relative bg-white overflow-hidden cursor-pointer dark:!bg-bg-card transition-all duration-300">
            <a href="{{ route('movies.show', $movie['id']) }}" class="block">
                <!-- Full Image - Backdrop Image with 16:9 Aspect Ratio -->
                <div class="relative overflow-hidden w-full aspect-video bg-gray-200 dark:bg-gray-800">
                    @if($movie['is_custom'] ?? false)
                        <img src="{{ $movie['backdrop_path'] ? (str_starts_with($movie['backdrop_path'], 'http') ? $movie['backdrop_path'] : asset('storage/' . $movie['backdrop_path'])) : ($movie['poster_path'] ? (str_starts_with($movie['poster_path'], 'http') ? $movie['poster_path'] : asset('storage/' . $movie['poster_path'])) : 'https://via.placeholder.com/780x439?text=No+Image') }}" 
                             alt="{{ $movie['title'] }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                             onerror="this.src='https://via.placeholder.com/780x439?text=No+Image'">
                    @else
                        <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($movie['backdrop_path'] ?? $movie['poster_path'] ?? null, 'w780') }}" 
                             alt="{{ $movie['title'] ?? 'Movie' }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                             onerror="this.src='https://via.placeholder.com/780x439?text=No+Image'">
                    @endif
                </div>
                
                <!-- Card Content -->
                <div class="p-3 bg-white dark:!bg-bg-card">
                    <!-- Title - Bold Text -->
                    <h2 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-accent transition-colors duration-300 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700; line-height: 1.4;">
                        {{ $movie['title'] ?? 'Unknown' }}
                        <span class="font-normal text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">(Movie)</span>
                    </h2>
                    
                    <!-- Content Details -->
                    <p class="text-gray-600 text-xs mb-1 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400; line-height: 1.4;">
                        @if($movie['is_custom'] ?? false)
                            @php
                                $typeLabel = ucfirst(str_replace('_', ' ', $movie['type'] ?? 'Movie'));
                                $dubbing = $movie['dubbing_language'] ? ucfirst($movie['dubbing_language']) . ' Dubbed' : '';
                            @endphp
                            {{ $typeLabel }}@if($dubbing) - {{ $dubbing }}@endif
                        @else
                            Movie - [ Full Movie ]
                        @endif
                    </p>
                    
                    <!-- Date - Smaller Lighter Gray Text -->
                    @if(!empty($movie['release_date']))
                    <p class="text-gray-500 text-xs dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        {{ \Carbon\Carbon::parse($movie['release_date'])->format('F d, Y') }}
                    </p>
                    @endif
                </div>
            </a>
        </article>
            @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <p class="text-gray-600 text-lg dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">No movies found.</p>
            </div>
            @endif

            <!-- Pagination -->
            @if($totalPages > 1)
            <div class="mt-8 flex justify-center items-center gap-2 flex-wrap">
        @if($currentPage > 1)
        <a href="{{ route('movies.index', ['type' => $type, 'page' => $currentPage - 1]) }}" 
           class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
            Previous
        </a>
        @endif
        
        @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
        <a href="{{ route('movies.index', ['type' => $type, 'page' => $i]) }}" 
           class="px-4 py-2 transition-all {{ $i === $currentPage ? 'bg-accent text-white dark:!bg-accent dark:!text-white' : 'bg-white hover:bg-gray-50 text-gray-900 dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white' }}" style="font-family: 'Poppins', sans-serif; font-weight: {{ $i === $currentPage ? '600' : '500' }};">
            {{ $i }}
        </a>
        @endfor
        
        @if($currentPage < $totalPages)
        <a href="{{ route('movies.index', ['type' => $type, 'page' => $currentPage + 1]) }}" 
           class="px-4 py-2 bg-accent hover:bg-accent-light text-white font-semibold transition-all" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            NEXT >
        </a>
            @endif
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
