@extends('layouts.app')

@section('title', 'Search' . ($query ? ' - ' . $query : '') . ' - Nazaarabox')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl md:text-3xl font-bold text-text-primary mb-8 pl-4 border-l-4 border-accent">
        Search Results{{ $query ? ' for "' . $query . '"' : '' }}
    </h2>

    @if(!empty($movies))
    <div class="mb-12">
        <h3 class="text-xl md:text-2xl font-semibold text-text-primary mb-6 pl-4">Movies</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-5 gap-3 sm:gap-4 md:gap-5 lg:gap-6">
            @foreach($movies as $movie)
            <a href="{{ route('movies.show', $movie['id']) }}" 
               class="group relative bg-bg-card rounded-xl overflow-hidden border border-border-secondary hover:border-accent/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-accent/20 cursor-pointer">
                <!-- Image Container -->
                <div class="relative overflow-hidden aspect-[2/3] bg-gradient-to-br from-bg-card to-bg-card-hover">
                    <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($movie['poster_path'] ?? null, 'w342') }}" 
                         alt="{{ $movie['title'] ?? 'Movie' }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                         onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <!-- Rating Badge -->
                    <div class="absolute top-2 right-2 bg-black/80 backdrop-blur-sm rounded-full px-2 py-1 flex items-center gap-1 border border-accent/30">
                        <span class="text-rating text-xs">★</span>
                        <span class="text-white text-xs font-bold">{{ number_format($movie['vote_average'] ?? 0, 1) }}</span>
                    </div>
                    
                    <!-- Hover Overlay with Info -->
                    <div class="absolute inset-0 flex items-end opacity-0 group-hover:opacity-100 transition-all duration-500 pb-3 px-3">
                        <div class="w-full">
                            <div class="bg-accent/90 backdrop-blur-sm rounded-lg px-3 py-2 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                                <p class="text-white text-xs font-semibold text-center">View Details</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card Content -->
                <div class="p-3 md:p-4 bg-gradient-to-b from-bg-card to-bg-card-hover">
                    <h3 class="text-sm md:text-base font-bold text-text-primary mb-2 line-clamp-2 group-hover:text-accent transition-colors duration-300 leading-tight">
                        {{ $movie['title'] ?? 'Unknown' }}
                    </h3>
                    <div class="flex items-center justify-between">
                        <span class="text-text-secondary text-xs md:text-sm font-medium">
                            {{ \Carbon\Carbon::parse($movie['release_date'] ?? '')->format('Y') ?? 'N/A' }}
                        </span>
                        <div class="flex items-center gap-1.5 bg-bg-card-hover rounded-full px-2 py-1">
                            <span class="text-rating text-xs">★</span>
                            <span class="font-bold text-text-primary text-xs">{{ number_format($movie['vote_average'] ?? 0, 1) }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if(!empty($tvShows))
    <div class="mb-12">
        <h3 class="text-xl md:text-2xl font-semibold text-text-primary mb-6 pl-4">TV Shows</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-5 gap-3 sm:gap-4 md:gap-5 lg:gap-6">
            @foreach($tvShows as $tvShow)
            <a href="{{ route('tv-shows.show', $tvShow['id']) }}" 
               class="group relative bg-bg-card rounded-xl overflow-hidden border border-border-secondary hover:border-accent/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-accent/20 cursor-pointer">
                <!-- Image Container -->
                <div class="relative overflow-hidden aspect-[2/3] bg-gradient-to-br from-bg-card to-bg-card-hover">
                    <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($tvShow['poster_path'] ?? null, 'w342') }}" 
                         alt="{{ $tvShow['name'] ?? 'TV Show' }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                         onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <!-- Rating Badge -->
                    <div class="absolute top-2 right-2 bg-black/80 backdrop-blur-sm rounded-full px-2 py-1 flex items-center gap-1 border border-accent/30">
                        <span class="text-rating text-xs">★</span>
                        <span class="text-white text-xs font-bold">{{ number_format($tvShow['vote_average'] ?? 0, 1) }}</span>
                    </div>
                    
                    <!-- Hover Overlay with Info -->
                    <div class="absolute inset-0 flex items-end opacity-0 group-hover:opacity-100 transition-all duration-500 pb-3 px-3">
                        <div class="w-full">
                            <div class="bg-accent/90 backdrop-blur-sm rounded-lg px-3 py-2 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                                <p class="text-white text-xs font-semibold text-center">View Details</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card Content -->
                <div class="p-3 md:p-4 bg-gradient-to-b from-bg-card to-bg-card-hover">
                    <h3 class="text-sm md:text-base font-bold text-text-primary mb-2 line-clamp-2 group-hover:text-accent transition-colors duration-300 leading-tight">
                        {{ $tvShow['name'] ?? 'Unknown' }}
                    </h3>
                    <div class="flex items-center justify-between">
                        <span class="text-text-secondary text-xs md:text-sm font-medium">
                            {{ \Carbon\Carbon::parse($tvShow['first_air_date'] ?? '')->format('Y') ?? 'N/A' }}
                        </span>
                        <div class="flex items-center gap-1.5 bg-bg-card-hover rounded-full px-2 py-1">
                            <span class="text-rating text-xs">★</span>
                            <span class="font-bold text-text-primary text-xs">{{ number_format($tvShow['vote_average'] ?? 0, 1) }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if(empty($movies) && empty($tvShows) && $query)
    <div class="text-center py-16">
        <p class="text-text-secondary text-lg md:text-xl">
            No results found for "<span class="text-text-primary font-semibold">{{ $query }}</span>"
        </p>
    </div>
    @endif
</div>
@endsection
