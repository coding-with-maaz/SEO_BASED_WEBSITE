

<?php $__env->startSection('title', 'Search' . ($query ? ' - ' . $query : '') . ' - Nazaarabox'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 xl:px-12 py-8">
    <!-- Breadcrumbs -->
    <?php if (isset($component)) { $__componentOriginal360d002b1b676b6f84d43220f22129e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal360d002b1b676b6f84d43220f22129e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumbs','data' => ['items' => [
        ['label' => 'Search' . ($query ? ' - ' . $query : ''), 'url' => null]
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Search' . ($query ? ' - ' . $query : ''), 'url' => null]
    ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal360d002b1b676b6f84d43220f22129e2)): ?>
<?php $attributes = $__attributesOriginal360d002b1b676b6f84d43220f22129e2; ?>
<?php unset($__attributesOriginal360d002b1b676b6f84d43220f22129e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal360d002b1b676b6f84d43220f22129e2)): ?>
<?php $component = $__componentOriginal360d002b1b676b6f84d43220f22129e2; ?>
<?php unset($__componentOriginal360d002b1b676b6f84d43220f22129e2); ?>
<?php endif; ?>
    
    <!-- Search Form & Filters -->
    <div class="mb-8">
        <form method="GET" action="<?php echo e(route('search')); ?>" class="space-y-4">
            <!-- Search Bar -->
            <div class="flex gap-2">
                <input type="text" 
                       name="q" 
                       value="<?php echo e($query); ?>" 
                       placeholder="Search movies, TV shows..." 
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white"
                       style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                <button type="submit" class="px-6 py-3 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Search
                </button>
            </div>

            <!-- Advanced Filters -->
            <div class="bg-white dark:!bg-bg-card border border-gray-200 dark:!border-border-secondary rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                        Advanced Filters
                    </h3>
                    <?php if($filters['genre'] || $filters['year'] || $filters['min_rating'] || $filters['type'] || $filters['sort_by'] !== 'relevance' || $filters['content_type'] !== 'all'): ?>
                    <a href="<?php echo e(route('search', ['q' => $query])); ?>" class="text-sm text-accent hover:underline" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                        Clear Filters
                    </a>
                    <?php endif; ?>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                    <!-- Content Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Content</label>
                        <select name="content_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white text-sm">
                            <option value="all" <?php echo e($filters['content_type'] === 'all' ? 'selected' : ''); ?>>All</option>
                            <option value="movies" <?php echo e($filters['content_type'] === 'movies' ? 'selected' : ''); ?>>Movies</option>
                            <option value="tv_shows" <?php echo e($filters['content_type'] === 'tv_shows' ? 'selected' : ''); ?>>TV Shows</option>
                        </select>
                    </div>

                    <!-- Genre -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Genre</label>
                        <select name="genre" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white text-sm">
                            <option value="">All Genres</option>
                            <?php $__currentLoopData = $allGenres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genreName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($genreName); ?>" <?php echo e($filters['genre'] === $genreName ? 'selected' : ''); ?>><?php echo e($genreName); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Year -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Year</label>
                        <select name="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white text-sm">
                            <option value="">All Years</option>
                            <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yearOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($yearOption); ?>" <?php echo e($filters['year'] == $yearOption ? 'selected' : ''); ?>><?php echo e($yearOption); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Rating -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Min Rating</label>
                        <select name="min_rating" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white text-sm">
                            <option value="">Any Rating</option>
                            <option value="1" <?php echo e($filters['min_rating'] == '1' ? 'selected' : ''); ?>>1+ ⭐</option>
                            <option value="2" <?php echo e($filters['min_rating'] == '2' ? 'selected' : ''); ?>>2+ ⭐</option>
                            <option value="3" <?php echo e($filters['min_rating'] == '3' ? 'selected' : ''); ?>>3+ ⭐</option>
                            <option value="4" <?php echo e($filters['min_rating'] == '4' ? 'selected' : ''); ?>>4+ ⭐</option>
                            <option value="5" <?php echo e($filters['min_rating'] == '5' ? 'selected' : ''); ?>>5+ ⭐</option>
                            <option value="6" <?php echo e($filters['min_rating'] == '6' ? 'selected' : ''); ?>>6+ ⭐</option>
                            <option value="7" <?php echo e($filters['min_rating'] == '7' ? 'selected' : ''); ?>>7+ ⭐</option>
                            <option value="8" <?php echo e($filters['min_rating'] == '8' ? 'selected' : ''); ?>>8+ ⭐</option>
                            <option value="9" <?php echo e($filters['min_rating'] == '9' ? 'selected' : ''); ?>>9+ ⭐</option>
                        </select>
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Type</label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white text-sm">
                            <option value="">All Types</option>
                            <?php $__currentLoopData = \App\Models\Content::getContentTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeKey => $typeLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($typeKey); ?>" <?php echo e($filters['type'] === $typeKey ? 'selected' : ''); ?>><?php echo e($typeLabel); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Sort By</label>
                        <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white text-sm">
                            <option value="relevance" <?php echo e($filters['sort_by'] === 'relevance' ? 'selected' : ''); ?>>Relevance</option>
                            <option value="newest" <?php echo e($filters['sort_by'] === 'newest' ? 'selected' : ''); ?>>Newest</option>
                            <option value="oldest" <?php echo e($filters['sort_by'] === 'oldest' ? 'selected' : ''); ?>>Oldest</option>
                            <option value="rating" <?php echo e($filters['sort_by'] === 'rating' ? 'selected' : ''); ?>>Highest Rating</option>
                            <option value="views" <?php echo e($filters['sort_by'] === 'views' ? 'selected' : ''); ?>>Most Views</option>
                            <option value="title" <?php echo e($filters['sort_by'] === 'title' ? 'selected' : ''); ?>>Title A-Z</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Area (2 columns on large screens) -->
        <div class="lg:col-span-2">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:!text-white mb-8 pl-4 border-l-4 border-accent" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
        Search Results<?php echo e($query ? ' for "' . $query . '"' : ''); ?>

        <?php if($filters['genre'] || $filters['year'] || $filters['min_rating'] || $filters['type']): ?>
            <span class="text-lg text-gray-600 dark:!text-text-secondary font-normal">(Filtered)</span>
        <?php endif; ?>
    </h2>

    <?php if(!empty($movies)): ?>
    <div class="mb-12">
        <h3 class="text-xl md:text-2xl font-semibold text-gray-900 dark:!text-white mb-6 pl-4" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Movies</h3>
        <!-- 2 Column Grid for Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="group relative bg-white overflow-hidden cursor-pointer dark:!bg-bg-card transition-all duration-300">
                <a href="<?php echo e(route('movies.show', $movie['id'] ?? ('custom_' . ($movie['id'] ?? '')))); ?>" class="block">
                    <!-- Full Image - Backdrop Image with 16:9 Aspect Ratio -->
                    <div class="relative overflow-hidden w-full aspect-video bg-gray-200 dark:bg-gray-800">
                        <?php
                            $backdropPath = !empty($movie['backdrop_path']) ? $movie['backdrop_path'] : null;
                            $posterPath = !empty($movie['poster_path']) ? $movie['poster_path'] : null;
                            $imagePath = $backdropPath ?? $posterPath;
                            
                            // Handle image URL for custom content
                            $imageUrl = null;
                            if ($imagePath) {
                                if (str_starts_with($imagePath, 'http')) {
                                    $imageUrl = $imagePath;
                                } elseif (isset($movie['is_custom']) && $movie['is_custom']) {
                                    $contentType = $movie['content_type'] ?? 'custom';
                                    if (in_array($contentType, ['tmdb', 'article']) || str_starts_with($imagePath, '/')) {
                                        $imageUrl = app(\App\Services\TmdbService::class)->getImageUrl($imagePath, 'w780');
                                    } else {
                                        $imageUrl = asset('storage/' . $imagePath);
                                    }
                                } else {
                                    $imageUrl = app(\App\Services\TmdbService::class)->getImageUrl($imagePath, 'w780');
                                }
                            }
                        ?>
                        <img src="<?php echo e($imageUrl ?? 'https://via.placeholder.com/780x439?text=No+Image'); ?>" 
                             alt="<?php echo e($movie['title'] ?? 'Movie'); ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                             style="display: block !important; visibility: visible !important; opacity: 1 !important; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;"
                             onerror="this.src='https://via.placeholder.com/780x439?text=No+Image'">
                        
                        <?php
                            $contentTypes = \App\Models\Content::getContentTypes();
                            $contentTypeName = 'Movie';
                            $dubbingLanguage = null;
                        ?>
                        
                        <!-- Content Type Badge - Top Left -->
                        <div class="absolute top-2 left-2 bg-accent text-white px-3 py-1 rounded-full text-xs font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600; z-index: 3; backdrop-filter: blur(4px); background-color: rgba(229, 9, 20, 0.9);">
                            <?php echo e($contentTypeName); ?>

                        </div>
                        
                        <!-- Beautiful Title Overlay - Always Visible -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex items-end pointer-events-none" style="z-index: 2;">
                            <div class="w-full p-4 pointer-events-auto">
                                <h3 class="text-xl font-bold text-white mb-1 line-clamp-2 group-hover:text-accent transition-colors duration-300" style="font-family: 'Poppins', sans-serif; font-weight: 800; text-shadow: 0 2px 8px rgba(0,0,0,0.9);">
                                    <?php echo e($movie['title'] ?? 'Unknown'); ?>

                                </h3>
                                <?php if($movie['release_date'] ?? null): ?>
                                <p class="text-sm text-gray-200" style="font-family: 'Poppins', sans-serif; font-weight: 500; text-shadow: 0 1px 4px rgba(0,0,0,0.8);">
                                    <?php echo e(\Carbon\Carbon::parse($movie['release_date'])->format('Y')); ?>

                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </a>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if(!empty($tvShows)): ?>
    <div class="mb-12">
        <h3 class="text-xl md:text-2xl font-semibold text-gray-900 dark:!text-white mb-6 pl-4" style="font-family: 'Poppins', sans-serif; font-weight: 600;">TV Shows</h3>
        <!-- 2 Column Grid for Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php $__currentLoopData = $tvShows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tvShow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="group relative bg-white overflow-hidden cursor-pointer dark:!bg-bg-card transition-all duration-300">
                <a href="<?php echo e(route('tv-shows.show', $tvShow['id'] ?? ('custom_' . ($tvShow['id'] ?? '')))); ?>" class="block">
                    <!-- Full Image - Backdrop Image with 16:9 Aspect Ratio -->
                    <div class="relative overflow-hidden w-full aspect-video bg-gray-200 dark:bg-gray-800" style="background-color: transparent !important;">
                        <?php
                            $backdropPath = !empty($tvShow['backdrop_path']) ? $tvShow['backdrop_path'] : null;
                            $posterPath = !empty($tvShow['poster_path']) ? $tvShow['poster_path'] : null;
                            $imagePath = $backdropPath ?? $posterPath;
                            
                            // Handle image URL for custom content
                            $imageUrl = null;
                            if ($imagePath) {
                                if (str_starts_with($imagePath, 'http')) {
                                    $imageUrl = $imagePath;
                                } elseif (isset($tvShow['is_custom']) && $tvShow['is_custom']) {
                                    $contentType = $tvShow['content_type'] ?? 'custom';
                                    if (in_array($contentType, ['tmdb', 'article']) || str_starts_with($imagePath, '/')) {
                                        $imageUrl = app(\App\Services\TmdbService::class)->getImageUrl($imagePath, 'w780');
                                    } else {
                                        $imageUrl = asset('storage/' . $imagePath);
                                    }
                                } else {
                                    $imageUrl = app(\App\Services\TmdbService::class)->getImageUrl($imagePath, 'w780');
                                }
                            }
                        ?>
                        <img src="<?php echo e($imageUrl ?? 'https://via.placeholder.com/780x439?text=No+Image'); ?>" 
                             alt="<?php echo e($tvShow['name'] ?? 'TV Show'); ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                             style="display: block !important; visibility: visible !important; opacity: 1 !important; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;"
                             onerror="this.src='https://via.placeholder.com/780x439?text=No+Image'">
                        
                        <?php
                            $contentTypes = \App\Models\Content::getContentTypes();
                            $contentTypeName = 'TV Show';
                            $dubbingLanguage = null;
                        ?>
                        
                        <!-- Content Type Badge - Top Left -->
                        <div class="absolute top-2 left-2 bg-accent text-white px-3 py-1 rounded-full text-xs font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600; z-index: 3; backdrop-filter: blur(4px); background-color: rgba(229, 9, 20, 0.9);">
                            <?php echo e($contentTypeName); ?>

                        </div>
                        
                        <!-- Beautiful Title Overlay - Always Visible -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex items-end pointer-events-none" style="z-index: 2;">
                            <div class="w-full p-4 pointer-events-auto">
                                <h3 class="text-xl font-bold text-white mb-1 line-clamp-2 group-hover:text-accent transition-colors duration-300" style="font-family: 'Poppins', sans-serif; font-weight: 800; text-shadow: 0 2px 8px rgba(0,0,0,0.9);">
                                    <?php echo e($tvShow['name'] ?? 'Unknown'); ?>

                                </h3>
                                <?php if($tvShow['first_air_date'] ?? null): ?>
                                <p class="text-sm text-gray-200" style="font-family: 'Poppins', sans-serif; font-weight: 500; text-shadow: 0 1px 4px rgba(0,0,0,0.8);">
                                    <?php echo e(\Carbon\Carbon::parse($tvShow['first_air_date'])->format('Y')); ?>

                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </a>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if(empty($movies) && empty($tvShows) && $query): ?>
    <div class="text-center py-16">
        <p class="text-gray-600 dark:!text-text-secondary text-lg md:text-xl" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
            No results found for "<span class="text-gray-900 dark:!text-white font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;"><?php echo e($query); ?></span>"
        </p>
    </div>
    <?php endif; ?>
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
                    <?php if(!empty($popularContent)): ?>
                        <?php $__currentLoopData = $popularContent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $routeName = in_array($item->type, ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']) ? 'tv-shows.show' : 'movies.show';
                            $itemId = $item->slug ?? ('custom_' . $item->id);
                            $posterPath = $item->poster_path;
                            $imageUrl = null;
                            
                            if ($posterPath) {
                                $contentType = $item->content_type ?? 'custom';
                                if (str_starts_with($posterPath, '/') || in_array($contentType, ['tmdb', 'article'])) {
                                    $imageUrl = app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w185');
                                } elseif (str_starts_with($posterPath, 'http')) {
                                    $imageUrl = $posterPath;
                                } else {
                                    $imageUrl = asset('storage/' . $posterPath);
                                }
                            }
                        ?>
                        <a href="<?php echo e(route($routeName, $itemId)); ?>" class="flex gap-3 group hover:bg-gray-50 p-2 rounded-lg transition-all dark:!hover:bg-bg-card-hover">
                            <div class="flex-shrink-0 w-16 h-24 rounded overflow-hidden bg-gray-100 dark:!bg-bg-card-hover">
                                <img src="<?php echo e($imageUrl ?? 'https://via.placeholder.com/185x278?text=No+Image'); ?>" 
                                     alt="<?php echo e($item->title); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                     onerror="this.src='https://via.placeholder.com/185x278?text=No+Image'">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 group-hover:text-accent transition-colors line-clamp-2 mb-1 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600; line-height: 1.4;">
                                    <?php echo e($item->title ?? 'Unknown'); ?>

                                </h4>
                                <p class="text-gray-600 text-xs mb-1 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                    <?php echo e($item->release_date ? $item->release_date->format('Y') : 'N/A'); ?>

                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600 text-xs dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                        👁 <?php echo e(number_format($item->views ?? 0)); ?> views
                                    </span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <p class="text-gray-600 text-sm dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">No popular content available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\k\Desktop\Nazaarabox\resources\views/search/index.blade.php ENDPATH**/ ?>