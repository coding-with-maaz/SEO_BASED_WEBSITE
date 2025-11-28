@extends('layouts.app')

@section('title', ($movie['title'] ?? 'Movie') . ' - Nazaarabox')

@section('content')
@php
    $title = $movie['title'] ?? 'Unknown';
    $originalTitle = $movie['original_title'] ?? $title;
    $rating = $movie['vote_average'] ?? 0;
    $releaseDate = $movie['release_date'] ?? null;
    $duration = $movie['runtime'] ?? null;
    $budget = $movie['budget'] ?? null;
    $revenue = $movie['revenue'] ?? null;
    $country = isset($movie['production_countries'][0]) ? $movie['production_countries'][0]['name'] : null;
    $language = isset($movie['spoken_languages'][0]) ? $movie['spoken_languages'][0]['name'] : null;
    $director = null;
    if (isset($movie['credits']['crew'])) {
        foreach ($movie['credits']['crew'] as $crew) {
            if ($crew['job'] === 'Director') {
                $director = $crew['name'];
                break;
            }
        }
    }
    $genres = $movie['genres'] ?? [];
    $cast = $movie['credits']['cast'] ?? [];
    $description = $movie['overview'] ?? '';
    $posterPath = $movie['poster_path'] ?? null;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Poster -->
        <div class="lg:col-span-1">
            @if(isset($isCustom) && $isCustom)
                <img src="{{ $posterPath ? (str_starts_with($posterPath, 'http') ? $posterPath : asset('storage/' . $posterPath)) : 'https://via.placeholder.com/500x750?text=No+Image' }}" 
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
        
        <!-- Details Header -->
        <div class="lg:col-span-2">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 800;">
                {{ $title }}
            </h1>
            
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
                @if($releaseDate)
                <span class="text-gray-600 dark:!text-text-secondary">•</span>
                <span class="text-gray-600 dark:!text-text-secondary">{{ \Carbon\Carbon::parse($releaseDate)->format('Y') }}</span>
                @endif
                @if($duration)
                <span class="text-gray-600 dark:!text-text-secondary">•</span>
                <span class="text-gray-600 dark:!text-text-secondary">{{ floor($duration / 60) }}h {{ $duration % 60 }}m</span>
                @endif
            </div>

            @if(!empty($genres))
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach($genres as $genre)
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm border border-gray-200 dark:!bg-bg-card dark:!text-text-secondary dark:!border-border-primary" style="font-family: 'Poppins', sans-serif; font-weight: 500;">{{ $genre['name'] }}</span>
                @endforeach
            </div>
            @endif

            @if($description)
            <p class="text-gray-600 dark:!text-text-secondary leading-relaxed text-base md:text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                {{ $description }}
            </p>
            @endif
        </div>
    </div>

    <!-- Details Section -->
    <div class="bg-white border border-gray-200 p-6 mb-8 dark:!bg-bg-card dark:!border-border-secondary rounded-lg">
        <h2 class="text-xl font-bold text-gray-900 mb-4 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @if($releaseDate)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Released:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    {{ \Carbon\Carbon::parse($releaseDate)->format('M d, Y') }}
                </span>
            </div>
            @endif
            
            @if($duration)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Duration:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ floor($duration / 60) }}h {{ $duration % 60 }}m</span>
            </div>
            @endif
            
            @if($country)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Country:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $country }}</span>
            </div>
            @endif
            
            @if($language)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Language:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $language }}</span>
            </div>
            @endif
            
            @if($director)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Director:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $director }}</span>
            </div>
            @endif
            
            @if($budget)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Budget:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">${{ number_format($budget) }}</span>
            </div>
            @endif
            
            @if($revenue)
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Revenue:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">${{ number_format($revenue) }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Watch/Download Section for Custom Movies -->
    @if(isset($isCustom) && $isCustom && isset($content) && ($content->watch_link || $content->download_link))
    <div class="bg-white border border-gray-200 p-6 mb-8 dark:!bg-bg-card dark:!border-border-secondary rounded-lg">
        <h2 class="text-xl font-bold text-gray-900 mb-4 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Watch & Download</h2>
        <div class="space-y-3">
            @if($content->watch_link)
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:!bg-bg-card-hover rounded-lg hover:bg-gray-100 dark:!hover:bg-bg-card transition-colors">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full border-2 border-red-500 flex items-center justify-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    </span>
                    <span class="text-gray-900 dark:!text-white font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Watch Online</span>
                </div>
                <a href="{{ $content->watch_link }}" target="_blank" class="px-4 py-2 bg-accent hover:bg-accent-light text-white font-semibold rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Watch Now
                </a>
            </div>
            @endif
            
            @if($content->download_link)
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:!bg-bg-card-hover rounded-lg hover:bg-gray-100 dark:!hover:bg-bg-card transition-colors">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full border-2 border-green-500 flex items-center justify-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    </span>
                    <span class="text-gray-900 dark:!text-white font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Download</span>
                </div>
                <a href="{{ $content->download_link }}" target="_blank" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Download
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Cast Section -->
    @if(!empty($cast))
    <div class="mb-8">
        <h3 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Cast</h3>
        <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
            @foreach(array_slice($cast, 0, 10) as $castMember)
            <div class="min-w-[100px] text-center flex-shrink-0">
                @if(isset($castMember['profile_path']))
                <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($castMember['profile_path'], 'w185') }}" 
                     alt="{{ $castMember['name'] }}" 
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
                <p class="text-sm font-medium text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">{{ $castMember['name'] ?? 'Unknown' }}</p>
                @if(isset($castMember['character']))
                <p class="text-xs text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    {{ $castMember['character'] }}
                </p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($movie['videos']['results']) && count($movie['videos']['results']) > 0)
    <div class="mb-12">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:!text-white mb-6 pl-4 border-l-4 border-accent" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Trailers
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach(array_slice($movie['videos']['results'], 0, 3) as $video)
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

    @if(isset($movie['recommendations']['results']) && count($movie['recommendations']['results']) > 0)
    <div class="mt-12 mb-12">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:!text-white mb-6 pl-4 border-l-4 border-accent" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Recommended Movies
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-5 gap-3 sm:gap-4 md:gap-5 lg:gap-6">
            @foreach(array_slice($movie['recommendations']['results'], 0, 10) as $recommended)
            <a href="{{ route('movies.show', $recommended['id']) }}" 
               class="group relative bg-white dark:!bg-bg-card rounded-xl overflow-hidden border border-gray-200 dark:!border-border-primary hover:border-accent/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-accent/20 cursor-pointer">
                <!-- Image Container -->
                <div class="relative overflow-hidden aspect-[2/3] bg-gray-100 dark:!bg-bg-card-hover">
                    <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($recommended['poster_path'] ?? null, 'w342') }}" 
                         alt="{{ $recommended['title'] ?? 'Movie' }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                         onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <!-- Rating Badge -->
                    <div class="absolute top-2 right-2 bg-black/80 dark:!bg-black/90 backdrop-blur-sm rounded-full px-2 py-1 flex items-center gap-1 border border-accent/30">
                        <span class="text-yellow-500 text-xs">★</span>
                        <span class="text-white text-xs font-bold">{{ number_format($recommended['vote_average'] ?? 0, 1) }}</span>
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
                        {{ $recommended['title'] ?? 'Unknown' }}
                    </h3>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:!text-text-secondary text-xs md:text-sm font-medium" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            {{ \Carbon\Carbon::parse($recommended['release_date'] ?? '')->format('Y') ?? 'N/A' }}
                        </span>
                        <div class="flex items-center gap-1.5 bg-gray-100 dark:!bg-bg-card-hover rounded-full px-2 py-1">
                            <span class="text-yellow-500 text-xs">★</span>
                            <span class="font-bold text-gray-900 dark:!text-white text-xs" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($recommended['vote_average'] ?? 0, 1) }}</span>
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
</style>
@endsection
