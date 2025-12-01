@props(['items', 'title', 'routeName' => 'movies.show'])

@if(!empty($items) && count($items) > 0)
<div class="mt-12 mb-12">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:!text-white mb-6 pl-4 border-l-4 border-accent" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
        {{ $title }}
    </h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-5 gap-3 sm:gap-4 md:gap-5 lg:gap-6">
        @foreach(array_slice($items, 0, 10) as $item)
        @php
            $itemId = $item['id'] ?? 'unknown';
            $itemTitle = $item['title'] ?? ($item['name'] ?? 'Unknown');
            $posterPath = $item['poster_path'] ?? null;
            $releaseDate = $item['release_date'] ?? ($item['first_air_date'] ?? null);
            $rating = $item['vote_average'] ?? 0;
            $contentType = $item['content_type'] ?? 'custom';
            
            // Get image URL - handle both database and TMDB content
            $imageUrl = null;
            if ($posterPath) {
                if (str_starts_with($posterPath, 'http')) {
                    $imageUrl = $posterPath;
                } elseif (in_array($contentType, ['tmdb', 'article']) || str_starts_with($posterPath, '/')) {
                    // TMDB or Article content - use TMDB service
                    $imageUrl = app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w342');
                } else {
                    // Database content with custom image
                    $imageUrl = $posterPath;
                }
            }
        @endphp
        <a href="{{ route($routeName, $itemId) }}" 
           class="group relative bg-white dark:!bg-bg-card rounded-xl overflow-hidden border border-gray-200 dark:!border-border-primary hover:border-accent/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-accent/20 cursor-pointer">
            <!-- Image Container -->
            <div class="relative overflow-hidden aspect-[2/3] bg-gray-100 dark:!bg-bg-card-hover">
                <img src="{{ $imageUrl ?? 'https://via.placeholder.com/300x450?text=No+Image' }}" 
                     alt="{{ $itemTitle }}" 
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                     onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <!-- Rating Badge -->
                <div class="absolute top-2 right-2 bg-black/80 dark:!bg-black/90 backdrop-blur-sm rounded-full px-2 py-1 flex items-center gap-1 border border-accent/30">
                    <span class="text-yellow-500 text-xs">★</span>
                    <span class="text-white text-xs font-bold">{{ number_format($rating, 1) }}</span>
                </div>
                
                <!-- Title Overlay -->
                <div class="absolute bottom-0 left-0 right-0 p-3 transform translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                    <h3 class="text-white font-bold text-sm mb-1 line-clamp-2" style="font-family: 'Poppins', sans-serif; font-weight: 700; text-shadow: 0 2px 8px rgba(0,0,0,0.9);">
                        {{ $itemTitle }}
                    </h3>
                    @if($releaseDate)
                    <p class="text-gray-300 text-xs" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        {{ \Carbon\Carbon::parse($releaseDate)->format('Y') }}
                    </p>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

