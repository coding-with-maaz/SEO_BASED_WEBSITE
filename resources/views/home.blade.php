@extends('layouts.app')

@section('title', 'Home - Nazaarabox')

@section('content')
<!-- Hero Section with Search -->
<section class="relative overflow-hidden mb-12" style="width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); background: linear-gradient(to bottom right, #1a1a1a, #0d0d0d, #000000);">
    <!-- Content -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="text-center">
            <!-- Main Heading -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6" style="font-family: 'Poppins', sans-serif; font-weight: 800; line-height: 1.2;">
                Discover Movies & TV Shows
            </h1>
            
            <!-- Subheading -->
            <p class="text-base md:text-lg text-gray-300 mb-10 max-w-2xl mx-auto leading-relaxed" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Search thousands of movies and TV series. Watch, download, and enjoy your favorite content.
            </p>
            
            <!-- Search Form -->
            <form action="{{ route('search') }}" method="GET" class="max-w-2xl mx-auto mb-8">
                <div class="relative">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Search for movies, TV shows..." 
                        class="w-full px-6 py-4 pr-20 text-gray-800 bg-gray-200 border-0 rounded-full focus:outline-none focus:ring-2 focus:ring-accent/50 text-base transition-all duration-300"
                        style="font-family: 'Poppins', sans-serif; font-weight: 400;"
                        autocomplete="off"
                        value="{{ request('q') }}">
                    <button 
                        type="submit" 
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 px-6 py-3 bg-accent hover:bg-accent-light text-white rounded-full font-semibold transition-all duration-300 flex items-center gap-2 shadow-lg"
                        style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
                    </button>
                </div>
            </form>
            
            <!-- Quick Links -->
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('movies.index') }}" class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-full transition-all duration-300 text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    Popular Movies
                </a>
                <a href="{{ route('tv-shows.index') }}" class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-full transition-all duration-300 text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    TV Shows
                </a>
                <a href="{{ route('movies.index', ['type' => 'top_rated']) }}" class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-full transition-all duration-300 text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    Top Rated
                </a>
            </div>
            </div>
        </div>
    
    <!-- Bottom Wave -->
    <div class="absolute bottom-0 left-0 right-0 w-full wave-separator" style="pointer-events: none; z-index: 10;">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" class="w-full" style="height: 100px; display: block;">
            <defs>
                <!-- Beautiful shadow filter with glow for dark mode -->
                <filter id="waveShadowGlow" x="-50%" y="-150%" width="200%" height="400%">
                    <!-- Outer red glow -->
                    <feGaussianBlur in="SourceAlpha" stdDeviation="10" result="blur1"/>
                    <feOffset dx="0" dy="-12" result="offset1"/>
                    <feFlood flood-color="#E50914" flood-opacity="0.6"/>
                    <feComposite in2="offset1" operator="in" result="glow1"/>
                    
                    <!-- Middle glow layer -->
                    <feGaussianBlur in="SourceAlpha" stdDeviation="6" result="blur2"/>
                    <feOffset dx="0" dy="-8" result="offset2"/>
                    <feFlood flood-color="#E50914" flood-opacity="0.8"/>
                    <feComposite in2="offset2" operator="in" result="glow2"/>
                    
                    <!-- Inner shadow for depth -->
                    <feGaussianBlur in="SourceAlpha" stdDeviation="4" result="blur3"/>
                    <feOffset dx="0" dy="-4" result="offset3"/>
                    <feFlood flood-color="#000000" flood-opacity="0.4"/>
                    <feComposite in2="offset3" operator="in" result="shadow1"/>
                    
                    <!-- Merge all effects -->
                    <feMerge>
                        <feMergeNode in="glow1"/>
                        <feMergeNode in="glow2"/>
                        <feMergeNode in="shadow1"/>
                        <feMergeNode in="SourceGraphic"/>
                    </feMerge>
                </filter>
            </defs>
            <!-- Wave path - color changes with dark mode -->
            <path id="wavePath" d="M0,120 L0,90 C120,70 240,50 360,60 C480,70 600,50 720,60 C840,70 960,50 1080,60 C1200,70 1320,50 1440,60 L1440,120 Z" fill="#FFFFFF"></path>
        </svg>
    </div>
    
    <style>
        /* Light mode: white wave */
        .wave-separator #wavePath {
            fill: #FFFFFF;
            transition: fill 0.3s ease;
        }
        
        /* Dark mode: red wave with beautiful shadow and glow */
        html.dark .wave-separator #wavePath {
            fill: #E50914;
            filter: url(#waveShadowGlow);
        }
        
        /* Additional CSS glow effect for extra beauty in dark mode */
        html.dark .wave-separator {
            filter: drop-shadow(0 -10px 30px rgba(229, 9, 20, 0.5)) drop-shadow(0 -5px 15px rgba(229, 9, 20, 0.3));
        }
    </style>
</section>

<div class="w-full px-4 sm:px-6 lg:px-8 xl:px-12 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Area (2 columns on large screens) -->
        <div class="lg:col-span-2">
            <!-- 2 Column Grid for Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(!empty($allContent))
                @foreach($allContent as $item)
                <article class="group relative bg-white overflow-hidden cursor-pointer dark:!bg-bg-card transition-all duration-300">
                    <a href="{{ $item['type'] === 'movie' ? route('movies.show', $item['id']) : route('tv-shows.show', $item['id']) }}" class="block">
                        <!-- Full Image - Backdrop Image with 16:9 Aspect Ratio -->
                        <div class="relative overflow-hidden w-full aspect-video bg-gray-200 dark:bg-gray-800">
                            @php
                                $imageUrl = null;
                                // Prioritize backdrop image (custom or TMDB), fallback to poster
                                $backdropPath = !empty($item['backdrop']) ? $item['backdrop'] : null;
                                $posterPath = !empty($item['poster']) ? $item['poster'] : null;
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
                                 alt="{{ $item['title'] }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                 onerror="this.src='https://via.placeholder.com/780x439?text=No+Image'">
                            
                            <!-- Beautiful Title Overlay - Always Visible -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex items-end">
                                <div class="w-full p-4">
                                    <h3 class="text-xl font-bold text-white mb-1 line-clamp-2 group-hover:text-accent transition-colors duration-300" style="font-family: 'Poppins', sans-serif; font-weight: 800; text-shadow: 0 2px 8px rgba(0,0,0,0.9);">
                                        {{ $item['title'] }}
                                    </h3>
                                    @if($item['date'])
                                    <p class="text-sm text-gray-200" style="font-family: 'Poppins', sans-serif; font-weight: 500; text-shadow: 0 1px 4px rgba(0,0,0,0.8);">
                                        {{ \Carbon\Carbon::parse($item['date'])->format('Y') }}
                                    </p>
                                    @endif
                                </div>
                            </div>
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
                                @if($item['is_custom'] ?? false)
                                    @php
                                        $typeLabel = ucfirst(str_replace('_', ' ', $item['content_type_name'] ?? $item['type'] ?? 'Movie'));
                                        $dubbing = $item['dubbing_language'] ? ucfirst($item['dubbing_language']) . ' Dubbed' : '';
                                    @endphp
                                    {{ $typeLabel }}@if($dubbing) - {{ $dubbing }}@endif
                                @else
                                    @if($item['type'] === 'movie')
                                        Movie - [ Full Movie ]
                                    @else
                                        TV Series - [ Episode 1 ADD ]
                                    @endif
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
                @else
                <div class="col-span-2 text-center py-16">
                    <p class="text-gray-600 dark:!text-text-secondary text-lg md:text-xl" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        No content available at the moment.
                    </p>
                </div>
                @endif
            </div>

            <!-- Pagination -->
            @if(isset($totalPages) && $totalPages > 1)
            <div class="mt-8 flex justify-center items-center gap-2 flex-wrap">
                @if($currentPage > 1)
                <a href="{{ route('home', ['page' => $currentPage - 1]) }}" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    Previous
                </a>
                @endif
                
                @php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                @endphp
                
                @if($startPage > 1)
                    <a href="{{ route('home', ['page' => 1]) }}" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" style="font-family: 'Poppins', sans-serif; font-weight: 500;">1</a>
                    @if($startPage > 2)
                    <span class="text-gray-600 px-2 dark:!text-text-secondary">…</span>
                    @endif
                @endif
                
                @for($i = $startPage; $i <= $endPage; $i++)
                <a href="{{ route('home', ['page' => $i]) }}" 
                   class="px-4 py-2 transition-all {{ $i === $currentPage ? 'bg-accent text-white dark:!bg-accent dark:!text-white' : 'bg-white hover:bg-gray-50 text-gray-900 dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white' }}" style="font-family: 'Poppins', sans-serif; font-weight: {{ $i === $currentPage ? '600' : '500' }};">
                    {{ $i }}
                </a>
                @endfor
                
                @if($endPage < $totalPages)
                    @if($endPage < $totalPages - 1)
                    <span class="text-gray-600 px-2 dark:!text-text-secondary">…</span>
                    @endif
                    <a href="{{ route('home', ['page' => $totalPages]) }}" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" style="font-family: 'Poppins', sans-serif; font-weight: 500;">{{ $totalPages }}</a>
                @endif
                
                @if($currentPage < $totalPages)
                <a href="{{ route('home', ['page' => $currentPage + 1]) }}" class="px-4 py-2 bg-accent hover:bg-accent-light text-white font-semibold transition-all" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
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

            <!-- Popular Section -->
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
                                if (($item->content_type ?? 'custom') === 'tmdb') {
                                    $imageUrl = app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w185');
                                } elseif (str_starts_with($posterPath, 'http') || str_starts_with($posterPath, '//')) {
                                    $imageUrl = $posterPath;
                                } else {
                                    // Custom poster - use path directly (already contains full URL)
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
