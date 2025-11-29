@extends('layouts.app')

@section('title', 'Completed TV Shows & Series - Nazaarabox')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 xl:px-12 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Area (2 columns on large screens) -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Completed TV Shows & Series
            </h2>

            @php
                $allCompleted = [];
                
                // Add custom completed content
                if (!empty($customCompleted)) {
                    foreach ($customCompleted as $content) {
                        $allCompleted[] = [
                            'id' => $content->slug ?? ('custom_' . $content->id),
                            'slug' => $content->slug,
                            'name' => $content->title,
                            'first_air_date' => $content->release_date ? $content->release_date->format('Y-m-d') : null,
                            'end_date' => $content->end_date ? $content->end_date->format('Y-m-d') : null,
                            'backdrop_path' => $content->backdrop_path,
                            'poster_path' => $content->poster_path,
                            'is_custom' => true,
                            'content_id' => $content->id,
                            'dubbing_language' => $content->dubbing_language,
                            'type' => $content->type,
                            'rating' => $content->rating ?? 0,
                            'episode_count' => $content->episode_count,
                        ];
                    }
                }
                
                // Sort by end date (most recently completed first), then by release date
                usort($allCompleted, function($a, $b) {
                    $endDateA = $a['end_date'] ?? $a['first_air_date'] ?? '1970-01-01';
                    $endDateB = $b['end_date'] ?? $b['first_air_date'] ?? '1970-01-01';
                    return strcmp($endDateB, $endDateA);
                });
            @endphp

            @if(!empty($allCompleted))
            <!-- 2 Column Grid for Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($allCompleted as $item)
                <article class="group relative bg-white overflow-hidden cursor-pointer dark:!bg-bg-card transition-all duration-300">
                    <a href="{{ route('tv-shows.show', $item['id']) }}" class="block">
                        <!-- Full Image - Backdrop Image with 16:9 Aspect Ratio -->
                        <div class="relative overflow-hidden w-full aspect-video bg-gray-200 dark:bg-gray-800">
                            @if($item['is_custom'] ?? false)
                                <img src="{{ $item['backdrop_path'] ? (str_starts_with($item['backdrop_path'], 'http') ? $item['backdrop_path'] : asset('storage/' . $item['backdrop_path'])) : ($item['poster_path'] ? (str_starts_with($item['poster_path'], 'http') ? $item['poster_path'] : asset('storage/' . $item['poster_path'])) : 'https://via.placeholder.com/780x439?text=No+Image') }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                     onerror="this.src='https://via.placeholder.com/780x439?text=No+Image'">
                            @endif
                        </div>
                        
                        <!-- Card Content -->
                        <div class="p-3 bg-white dark:!bg-bg-card">
                            <!-- Title - Bold Text -->
                            <h2 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-accent transition-colors duration-300 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700; line-height: 1.4;">
                                {{ $item['name'] ?? 'Unknown' }}
                                @php
                                    $typeLabel = ucfirst(str_replace('_', ' ', $item['type'] ?? 'TV Show'));
                                @endphp
                                <span class="font-normal text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">({{ $typeLabel }})</span>
                            </h2>
                            
                            <!-- Content Details -->
                            <p class="text-gray-600 text-xs mb-1 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400; line-height: 1.4;">
                                {{ $typeLabel }}
                                @if($item['dubbing_language'] ?? null)
                                    - {{ ucfirst($item['dubbing_language']) }} Dubbed
                                @endif
                                @if($item['first_air_date'] ?? null)
                                    • {{ \Carbon\Carbon::parse($item['first_air_date'])->format('Y') }}
                                    @if($item['end_date'] ?? null)
                                        - {{ \Carbon\Carbon::parse($item['end_date'])->format('Y') }}
                                    @endif
                                @endif
                                @if($item['episode_count'] ?? null)
                                    • {{ $item['episode_count'] }} Episodes
                                @endif
                                @if($item['rating'] ?? null && $item['rating'] > 0)
                                    • ★ {{ number_format($item['rating'], 1) }}
                                @endif
                            </p>
                            
                            <!-- Date -->
                            @if($item['end_date'] ?? null)
                            <p class="text-gray-500 text-xs dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                Completed: {{ \Carbon\Carbon::parse($item['end_date'])->format('M d, Y') }}
                            </p>
                            @elseif($item['first_air_date'] ?? null)
                            <p class="text-gray-500 text-xs dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                Released: {{ \Carbon\Carbon::parse($item['first_air_date'])->format('M d, Y') }}
                            </p>
                            @endif
                        </div>
                    </a>
                </article>
                @endforeach
            </div>
            @else
            <div class="text-center py-16">
                <p class="text-gray-600 dark:!text-text-secondary text-lg md:text-xl" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    No completed TV shows or series available at the moment.
                </p>
            </div>
            @endif
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

            <!-- Popular Now Section -->
            <div class="bg-white border border-gray-200 p-6 dark:!bg-bg-card dark:!border-border-secondary">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-200 pb-3 dark:!text-white dark:!border-border-primary" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Popular Now</h3>
                <div class="space-y-4">
                    @if(!empty($topRatedTvShows))
                        @foreach(array_slice($topRatedTvShows, 0, 5) as $tvShow)
                        <a href="{{ route('tv-shows.show', $tvShow['id']) }}" class="flex gap-3 group hover:bg-gray-50 p-2 rounded-lg transition-all dark:!hover:bg-bg-card-hover">
                            <div class="flex-shrink-0 w-16 h-24 rounded overflow-hidden bg-gray-100 dark:!bg-bg-card-hover">
                                <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($tvShow['poster_path'] ?? null, 'w185') }}" 
                                     alt="{{ $tvShow['name'] }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                     onerror="this.src='https://via.placeholder.com/185x278?text=No+Image'">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 group-hover:text-accent transition-colors line-clamp-2 mb-1 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600; line-height: 1.4;">
                                    {{ $tvShow['name'] ?? 'Unknown' }}
                                </h4>
                                <p class="text-gray-600 text-xs mb-1 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                    {{ \Carbon\Carbon::parse($tvShow['first_air_date'] ?? '')->format('Y') ?? 'N/A' }}
                                </p>
                                <div class="flex items-center gap-1">
                                    <span class="text-rating text-xs">★</span>
                                    <span class="text-gray-900 text-xs font-semibold dark:!text-white">{{ number_format($tvShow['vote_average'] ?? 0, 1) }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    @else
                        <p class="text-gray-600 text-sm dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">No popular TV shows available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

