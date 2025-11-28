@extends('layouts.app')

@section('title', ($tvShow['name'] ?? 'TV Show') . ' - Nazaarabox')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Poster -->
        <div class="lg:col-span-1">
            <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($tvShow['poster_path'] ?? null, 'w500') }}" 
                 alt="{{ $tvShow['name'] ?? 'TV Show' }}" 
                 class="w-full rounded-xl shadow-2xl"
                 onerror="this.src='https://via.placeholder.com/500x750?text=No+Image'">
        </div>
        
        <!-- Details -->
        <div class="lg:col-span-2">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-text-primary mb-4">
                {{ $tvShow['name'] ?? 'Unknown' }}
            </h1>
            
            <div class="flex flex-wrap items-center gap-4 mb-6">
                <div class="flex items-center gap-2 text-rating">
                    <span class="text-2xl">★</span>
                    <span class="text-xl font-bold text-text-primary">{{ number_format($tvShow['vote_average'] ?? 0, 1) }}/10</span>
                </div>
                <span class="text-text-secondary">•</span>
                <span class="text-text-secondary">{{ \Carbon\Carbon::parse($tvShow['first_air_date'] ?? '')->format('Y') ?? 'N/A' }}</span>
                @if(isset($tvShow['number_of_seasons']))
                <span class="text-text-secondary">•</span>
                <span class="text-text-secondary">{{ $tvShow['number_of_seasons'] }} Season{{ $tvShow['number_of_seasons'] > 1 ? 's' : '' }}</span>
                @endif
                @if(isset($tvShow['number_of_episodes']))
                <span class="text-text-secondary">•</span>
                <span class="text-text-secondary">{{ $tvShow['number_of_episodes'] }} Episode{{ $tvShow['number_of_episodes'] > 1 ? 's' : '' }}</span>
                @endif
            </div>

            @if(isset($tvShow['genres']) && is_array($tvShow['genres']))
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach($tvShow['genres'] as $genre)
                <span class="px-3 py-1 bg-bg-card text-text-secondary rounded-full text-sm border border-border-primary">
                    {{ $genre['name'] }}
                </span>
                @endforeach
            </div>
            @endif

            @if(isset($tvShow['overview']))
            <p class="text-text-secondary leading-relaxed mb-6 text-base md:text-lg">
                {{ $tvShow['overview'] }}
            </p>
            @endif

            @if(isset($tvShow['credits']['cast']) && count($tvShow['credits']['cast']) > 0)
            <div class="mt-8">
                <h3 class="text-xl font-bold text-text-primary mb-4">Cast</h3>
                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                    @foreach(array_slice($tvShow['credits']['cast'], 0, 10) as $cast)
                    <div class="min-w-[100px] text-center flex-shrink-0">
                        @if(isset($cast['profile_path']))
                        <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($cast['profile_path'], 'w185') }}" 
                             alt="{{ $cast['name'] }}" 
                             class="w-20 h-28 md:w-24 md:h-36 object-cover rounded-lg mb-2 shadow-lg"
                             onerror="this.style.display='none'">
                        @endif
                        <p class="text-sm font-medium text-text-primary">{{ $cast['name'] ?? 'Unknown' }}</p>
                        <p class="text-xs text-text-secondary mt-1">
                            {{ $cast['character'] ?? '' }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    @if(isset($tvShow['videos']['results']) && count($tvShow['videos']['results']) > 0)
    <div class="mb-12">
        <h2 class="text-2xl md:text-3xl font-bold text-text-primary mb-6 pl-4 border-l-4 border-accent">
            Trailers
        </h2>
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
    <div class="mb-12">
        <h2 class="text-2xl md:text-3xl font-bold text-text-primary mb-6 pl-4 border-l-4 border-accent">
            Recommended TV Shows
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-5 gap-3 sm:gap-4 md:gap-5 lg:gap-6">
            @foreach(array_slice($tvShow['recommendations']['results'], 0, 12) as $recommended)
            <a href="{{ route('tv-shows.show', $recommended['id']) }}" 
               class="group relative bg-bg-card rounded-xl overflow-hidden border border-border-secondary hover:border-accent/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-accent/20 cursor-pointer">
                <!-- Image Container -->
                <div class="relative overflow-hidden aspect-[2/3] bg-gradient-to-br from-bg-card to-bg-card-hover">
                    <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($recommended['poster_path'] ?? null) }}" 
                         alt="{{ $recommended['name'] ?? 'TV Show' }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                         onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <!-- Rating Badge -->
                    <div class="absolute top-2 right-2 bg-black/80 backdrop-blur-sm rounded-full px-2 py-1 flex items-center gap-1 border border-accent/30">
                        <span class="text-rating text-xs">★</span>
                        <span class="text-white text-xs font-bold">{{ number_format($recommended['vote_average'] ?? 0, 1) }}</span>
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
                        {{ $recommended['name'] ?? 'Unknown' }}
                    </h3>
                    <div class="flex items-center justify-between">
                        <span class="text-text-secondary text-xs md:text-sm font-medium">
                            {{ \Carbon\Carbon::parse($recommended['first_air_date'] ?? '')->format('Y') ?? 'N/A' }}
                        </span>
                        <div class="flex items-center gap-1.5 bg-bg-card-hover rounded-full px-2 py-1">
                            <span class="text-rating text-xs">★</span>
                            <span class="font-bold text-text-primary text-xs">{{ number_format($recommended['vote_average'] ?? 0, 1) }}</span>
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
