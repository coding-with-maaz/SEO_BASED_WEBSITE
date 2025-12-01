

<?php $__env->startSection('title', ($movie['title'] ?? 'Movie') . ' - Nazaarabox'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $title = $movie['title'] ?? 'Unknown';
    $originalTitle = $movie['original_title'] ?? $title;
    $rating = $movie['vote_average'] ?? 0;
    $releaseDate = $movie['release_date'] ?? null;
    $duration = $movie['runtime'] ?? null;
    $budget = $movie['budget'] ?? null;
    $revenue = $movie['revenue'] ?? null;
    $views = $movie['views'] ?? (isset($content) ? ($content->views ?? 0) : 0);
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
    $backdropPath = $movie['backdrop_path'] ?? (isset($content) ? $content->backdrop_path : null);
?>

<?php if(isset($isCustom) && $isCustom && isset($content) && $content->content_type === 'article' && $backdropPath): ?>
    <?php
        $backdropUrl = null;
        if ($backdropPath) {
            $contentType = $content->content_type ?? 'custom';
            if (str_starts_with($backdropPath, '/') || in_array($contentType, ['tmdb', 'article'])) {
                $backdropUrl = app(\App\Services\TmdbService::class)->getImageUrl($backdropPath, 'w1280');
            } elseif (str_starts_with($backdropPath, 'http')) {
                $backdropUrl = $backdropPath;
            } else {
                $backdropUrl = asset('storage/' . $backdropPath);
            }
        }
    ?>
    <!-- Backdrop Hero Section for Article Content -->
    <div class="relative w-full h-[60vh] min-h-[400px] max-h-[600px] overflow-hidden mb-8">
        <div class="absolute inset-0">
            <img src="<?php echo e($backdropUrl ?? 'https://via.placeholder.com/1920x1080?text=No+Backdrop'); ?>" 
                 alt="<?php echo e($title); ?>" 
                 class="w-full h-full object-cover"
                 onerror="this.src='https://via.placeholder.com/1920x1080?text=No+Backdrop'">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent"></div>
        </div>
        <div class="relative z-10 h-full flex items-end">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pb-12">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 drop-shadow-2xl" style="font-family: 'Poppins', sans-serif; font-weight: 800;">
                    <?php echo e($title); ?>

                </h1>
                <?php if($description): ?>
                <p class="text-lg md:text-xl text-gray-200 max-w-3xl line-clamp-3 drop-shadow-lg" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    <?php echo e($description); ?>

                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumbs -->
    <?php if (isset($component)) { $__componentOriginal360d002b1b676b6f84d43220f22129e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal360d002b1b676b6f84d43220f22129e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumbs','data' => ['items' => [
        ['label' => 'Movies', 'url' => route('movies.index')],
        ['label' => $title, 'url' => null]
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Movies', 'url' => route('movies.index')],
        ['label' => $title, 'url' => null]
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
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Poster -->
        <div class="lg:col-span-1">
            <?php if(isset($isCustom) && $isCustom): ?>
                <?php
                    $posterUrl = null;
                    if ($posterPath) {
                        $contentType = $content->content_type ?? 'custom';
                        // Check if it's a TMDB path (starts with /) or content_type is tmdb/article
                        if (str_starts_with($posterPath, '/') || in_array($contentType, ['tmdb', 'article'])) {
                            // Use TMDB service for TMDB paths
                            $posterUrl = app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w500');
                        } elseif (str_starts_with($posterPath, 'http')) {
                            // Full URL
                            $posterUrl = $posterPath;
                        } else {
                            // Local storage
                            $posterUrl = asset('storage/' . $posterPath);
                        }
                    }
                ?>
                <img src="<?php echo e($posterUrl ?? 'https://via.placeholder.com/500x750?text=No+Image'); ?>" 
                     alt="<?php echo e($title); ?>" 
                     class="w-full rounded-xl shadow-2xl"
                     onerror="this.src='https://via.placeholder.com/500x750?text=No+Image'">
            <?php else: ?>
                <img src="<?php echo e(app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w500')); ?>" 
                     alt="<?php echo e($title); ?>" 
                     class="w-full rounded-xl shadow-2xl"
                     onerror="this.src='https://via.placeholder.com/500x750?text=No+Image'">
            <?php endif; ?>
        </div>
        
        <!-- Details Header -->
        <div class="lg:col-span-2">
            <?php if(!(isset($isCustom) && $isCustom && isset($content) && $content->content_type === 'article' && $backdropPath)): ?>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 800;">
                <?php echo e($title); ?>

            </h1>
            <?php else: ?>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 800;">
                <?php echo e($title); ?>

            </h1>
            <?php endif; ?>
            
            <?php if($originalTitle !== $title): ?>
            <p class="text-lg text-gray-600 dark:!text-text-secondary mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                Original Title: <?php echo e($originalTitle); ?>

            </p>
            <?php endif; ?>
            
            <div class="flex flex-wrap items-center gap-4 mb-6">
                <div class="flex items-center gap-2 text-yellow-500">
                    <span class="text-2xl">★</span>
                    <span class="text-xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;"><?php echo e(number_format($rating, 1)); ?>/10</span>
                </div>
                <?php if($releaseDate): ?>
                <span class="text-gray-600 dark:!text-text-secondary">•</span>
                <span class="text-gray-600 dark:!text-text-secondary"><?php echo e(\Carbon\Carbon::parse($releaseDate)->format('Y')); ?></span>
                <?php endif; ?>
                <?php if($duration): ?>
                <span class="text-gray-600 dark:!text-text-secondary">•</span>
                <span class="text-gray-600 dark:!text-text-secondary"><?php echo e(floor($duration / 60)); ?>h <?php echo e($duration % 60); ?>m</span>
                <?php endif; ?>
                <?php if(isset($isCustom) && $isCustom && $views > 0): ?>
                <span class="text-gray-600 dark:!text-text-secondary">•</span>
                <div class="flex items-center gap-1 text-gray-600 dark:!text-text-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span class="font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;"><?php echo e(number_format($views)); ?> views</span>
                </div>
                <?php endif; ?>
            </div>

            <?php if(!empty($genres)): ?>
            <div class="flex flex-wrap gap-2 mb-6">
                <?php $__currentLoopData = $genres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm border border-gray-200 dark:!bg-bg-card dark:!text-text-secondary dark:!border-border-primary" style="font-family: 'Poppins', sans-serif; font-weight: 500;"><?php echo e($genre['name']); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            <?php if($description): ?>
            <p class="text-gray-600 dark:!text-text-secondary leading-relaxed text-base md:text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                <?php echo e($description); ?>

            </p>
            <?php endif; ?>
            
            <?php if(isset($isCustom) && $isCustom && isset($content) && $content->content_type === 'article' && $content->article_content): ?>
            <div class="mt-6 article-content prose prose-lg max-w-none dark:prose-invert" style="font-family: 'Poppins', sans-serif;">
                <?php echo $content->article_content; ?>

            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Details Section -->
    <div class="bg-white border border-gray-200 p-6 mb-8 dark:!bg-bg-card dark:!border-border-secondary rounded-lg">
        <h2 class="text-xl font-bold text-gray-900 mb-4 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <?php if($releaseDate): ?>
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Released:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    <?php echo e(\Carbon\Carbon::parse($releaseDate)->format('M d, Y')); ?>

                </span>
            </div>
            <?php endif; ?>
            
            <?php if($duration): ?>
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Duration:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;"><?php echo e(floor($duration / 60)); ?>h <?php echo e($duration % 60); ?>m</span>
            </div>
            <?php endif; ?>
            
            <?php if($country): ?>
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Country:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;"><?php echo e($country); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if($language): ?>
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Language:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;"><?php echo e($language); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if($director): ?>
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Director:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;"><?php echo e($director); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if(isset($isCustom) && $isCustom && $views > 0): ?>
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Views:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;"><?php echo e(number_format($views)); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if($budget): ?>
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Budget:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">$<?php echo e(number_format($budget)); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if($revenue): ?>
            <div>
                <span class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Revenue:</span>
                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">$<?php echo e(number_format($revenue)); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Video Player Section for Custom Movies -->
    <?php if(isset($isCustom) && $isCustom && isset($content)): ?>
    <?php
        // Get normalized active servers
        $servers = $content->getActiveServers();
        
        // If no servers but watch_link exists, create a default server
        if (empty($servers) && $content->watch_link) {
            $servers = [[
                'id' => 'default',
                'name' => 'Server 1',
                'url' => $content->watch_link,
                'quality' => 'HD',
                'active' => true,
                'sort_order' => 0
            ]];
        }
        
        // Get the first server as default for player
        $defaultServer = !empty($servers) ? reset($servers) : null;
        $currentServerUrl = $defaultServer['url'] ?? $content->watch_link ?? '';
        
        // Get all download links (from servers and content level)
        $downloadLinks = $content->getAllDownloadLinks();
    ?>
    
    <?php if(!empty($servers) || $content->watch_link): ?>
        <?php if($content->content_type === 'article'): ?>
            <!-- Article Content - Stylish Buttons -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:!from-bg-card dark:!to-bg-card-hover border border-gray-200 dark:!border-border-secondary rounded-xl p-8 mb-8 shadow-lg">
                <div class="text-center mb-8">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:!text-white mb-3" style="font-family: 'Poppins', sans-serif; font-weight: 800;">
                        Ready to Watch?
                    </h2>
                    <p class="text-gray-600 dark:!text-text-secondary text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        Click the button below to start streaming
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center max-w-2xl mx-auto">
                    <!-- Watch Now Button -->
                    <?php if(!empty($servers) || $content->watch_link): ?>
                        <?php
                            $watchUrl = !empty($servers) ? ($servers[0]['url'] ?? $content->watch_link) : $content->watch_link;
                        ?>
                        <a href="<?php echo e($watchUrl); ?>" 
                           target="_blank"
                           class="group relative w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-accent to-red-700 hover:from-red-700 hover:to-accent text-white font-bold rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center gap-3 overflow-hidden"
                           style="font-family: 'Poppins', sans-serif; font-weight: 700; min-width: 200px;">
                            <!-- Animated Background -->
                            <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform -skew-x-12 group-hover:translate-x-full"></div>
                            
                            <!-- Play Icon -->
                            <svg class="w-6 h-6 relative z-10" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            
                            <!-- Button Text -->
                            <span class="relative z-10 text-lg">Watch Now</span>
                            
                            <!-- Shine Effect -->
                            <div class="absolute inset-0 -top-2 -bottom-2 bg-gradient-to-r from-transparent via-white/30 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        </a>
                    <?php endif; ?>
                    
                    <!-- Download Button -->
                    <?php if(!empty($downloadLinks)): ?>
                        <?php
                            $primaryDownload = $downloadLinks[0] ?? null;
                        ?>
                        <?php if($primaryDownload): ?>
                        <a href="<?php echo e($primaryDownload['url']); ?>" 
                           target="_blank"
                           class="group relative w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-700 hover:from-emerald-700 hover:to-green-600 text-white font-bold rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center gap-3 overflow-hidden"
                           style="font-family: 'Poppins', sans-serif; font-weight: 700; min-width: 200px;">
                            <!-- Animated Background -->
                            <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform -skew-x-12 group-hover:translate-x-full"></div>
                            
                            <!-- Download Icon -->
                            <svg class="w-6 h-6 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            
                            <!-- Button Text -->
                            <span class="relative z-10 text-lg">Download</span>
                            
                            <!-- Shine Effect -->
                            <div class="absolute inset-0 -top-2 -bottom-2 bg-gradient-to-r from-transparent via-white/30 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Additional Download Options -->
                <?php if(!empty($downloadLinks) && count($downloadLinks) > 1): ?>
                <div class="mt-8 pt-6 border-t border-gray-300 dark:!border-border-primary">
                    <h3 class="text-lg font-semibold text-gray-900 dark:!text-white mb-4 text-center" style="font-family: 'Poppins', sans-serif; font-weight: 600;">More Download Options</h3>
                    <div class="flex flex-wrap gap-3 justify-center">
                        <?php $__currentLoopData = array_slice($downloadLinks, 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $download): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($download['url']); ?>" 
                           target="_blank"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:!bg-bg-card border-2 border-gray-300 dark:!border-border-primary hover:border-accent text-gray-700 dark:!text-white hover:text-accent font-semibold rounded-lg transition-all duration-300 hover:shadow-md transform hover:scale-105"
                           style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <?php echo e($download['name']); ?>

                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Server Selection (if multiple servers) -->
                <?php if(count($servers) > 1): ?>
                <div class="mt-6 pt-6 border-t border-gray-300 dark:!border-border-primary">
                    <h3 class="text-lg font-semibold text-gray-900 dark:!text-white mb-4 text-center" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Available Servers</h3>
                    <div class="flex flex-wrap gap-3 justify-center">
                        <?php $__currentLoopData = $servers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $server): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!empty($server['url'])): ?>
                            <a href="<?php echo e($server['url']); ?>" 
                               target="_blank"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:!bg-bg-card border-2 border-gray-300 dark:!border-border-primary hover:border-accent text-gray-700 dark:!text-white hover:text-accent font-semibold rounded-lg transition-all duration-300 hover:shadow-md transform hover:scale-105"
                               style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                <?php echo e($server['name'] ?? 'Server ' . ($index + 1)); ?><?php if(!empty($server['quality'])): ?> - <?php echo e($server['quality']); ?><?php endif; ?>
                            </a>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Regular Content - Video Player -->
            <div class="bg-white border border-gray-200 p-6 mb-8 dark:!bg-bg-card dark:!border-border-secondary rounded-lg">
                <h2 class="text-xl font-bold text-gray-900 mb-4 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Watch Movie</h2>
                
                <!-- Video Player Container -->
                <div class="mb-4">
                    <div class="relative w-full bg-black rounded-lg overflow-hidden" style="padding-bottom: 56.25%;">
                        <iframe id="moviePlayer" 
                                src="<?php echo e($currentServerUrl); ?>" 
                                class="absolute top-0 left-0 w-full h-full border-0" 
                                allow="autoplay; fullscreen" 
                                allowfullscreen
                                frameborder="0">
                        </iframe>
                    </div>
                </div>

                <!-- Server Selection -->
                <?php if(count($servers) > 1): ?>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-900 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Select Server:</label>
                    <div class="flex flex-wrap gap-2">
                        <?php $__currentLoopData = $servers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $server): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!empty($server['url'])): ?>
                            <button onclick="changeServer('<?php echo e($server['url']); ?>', this)" 
                                    class="server-btn px-4 py-2 rounded-lg transition-colors <?php echo e($index === 0 ? 'bg-accent text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:!bg-bg-card-hover dark:!text-text-secondary dark:!hover:bg-bg-card dark:!hover:text-white'); ?>"
                                    style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                                <?php echo e($server['name'] ?? 'Server ' . ($index + 1)); ?><?php if(!empty($server['quality'])): ?> - <?php echo e($server['quality']); ?><?php endif; ?>
                            </button>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Download Links -->
                <?php if(!empty($downloadLinks)): ?>
                <div class="mt-4 pt-4 border-t border-gray-200 dark:!border-border-secondary">
                    <h3 class="text-lg font-bold text-gray-900 mb-3 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Download</h3>
                    <div class="flex flex-wrap gap-3">
                        <?php $__currentLoopData = $downloadLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $download): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($download['url']); ?>" 
                           target="_blank" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors"
                           style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <?php echo e($download['name']); ?>

                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Cast Section - Show for both custom and TMDB content -->
    <?php if(!empty($cast)): ?>
    <div class="mb-8">
        <h3 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Cast</h3>
        <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
            <?php $__currentLoopData = array_slice($cast, 0, 10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $castMember): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="min-w-[100px] text-center flex-shrink-0">
                <?php
                    $profilePath = !empty($castMember['profile_path']) ? $castMember['profile_path'] : null;
                    $profileUrl = null;
                    if ($profilePath) {
                        // Check if it's a full URL
                        if (str_starts_with($profilePath, 'http')) {
                            $profileUrl = $profilePath;
                        } elseif (str_starts_with($profilePath, '/')) {
                            // TMDB path (starts with /) - use TMDB service
                            $profileUrl = app(\App\Services\TmdbService::class)->getImageUrl($profilePath, 'w185');
                        } else {
                            // Custom path - try to use directly or fallback to TMDB service
                            $profileUrl = $profilePath;
                        }
                    }
                    
                    // Check if we have cast slug for linking (database casts only)
                    $castSlug = $castMember['slug'] ?? null;
                    $castId = $castMember['id'] ?? null;
                ?>
                <a href="<?php echo e(($castSlug || $castId) ? route('cast.show', $castSlug ?? $castId) : '#'); ?>" class="block <?php echo e(!($castSlug || $castId) ? 'cursor-default' : 'hover:opacity-90 transition-opacity'); ?>">
                    <?php if($profileUrl): ?>
                    <img src="<?php echo e($profileUrl); ?>" 
                         alt="<?php echo e($castMember['name'] ?? 'Unknown'); ?>" 
                         class="w-20 h-28 md:w-24 md:h-36 object-cover rounded-lg mb-2 shadow-lg mx-auto"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-20 h-28 md:w-24 md:h-36 bg-gray-200 dark:bg-gray-800 rounded-lg mb-2 items-center justify-center hidden mx-auto">
                        <span class="text-gray-400 text-xs">No Photo</span>
                    </div>
                    <?php else: ?>
                    <div class="w-20 h-28 md:w-24 md:h-36 bg-gray-200 dark:bg-gray-800 rounded-lg mb-2 flex items-center justify-center mx-auto">
                        <span class="text-gray-400 text-xs">No Photo</span>
                    </div>
                    <?php endif; ?>
                    <p class="text-sm font-medium text-gray-900 dark:!text-white <?php echo e(($castSlug || $castId) ? 'hover:text-accent transition-colors' : ''); ?>" style="font-family: 'Poppins', sans-serif; font-weight: 600;"><?php echo e($castMember['name'] ?? 'Unknown'); ?></p>
                    <?php if(!empty($castMember['character'])): ?>
                    <p class="text-xs text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        <?php echo e($castMember['character']); ?>

                    </p>
                    <?php endif; ?>
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if(isset($movie['videos']['results']) && count($movie['videos']['results']) > 0): ?>
    <div class="mb-12">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:!text-white mb-6 pl-4 border-l-4 border-accent" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Trailers
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = array_slice($movie['videos']['results'], 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($video['site'] === 'YouTube'): ?>
            <div class="relative pb-[56.25%] h-0 overflow-hidden rounded-xl">
                <iframe src="https://www.youtube.com/embed/<?php echo e($video['key']); ?>" 
                        class="absolute top-0 left-0 w-full h-full border-0"
                        allowfullscreen></iframe>
            </div>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if(isset($movie['recommendations']['results']) && count($movie['recommendations']['results']) > 0): ?>
    <div class="mt-12 mb-12">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:!text-white mb-6 pl-4 border-l-4 border-accent" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Recommended Movies
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-5 gap-3 sm:gap-4 md:gap-5 lg:gap-6">
            <?php $__currentLoopData = array_slice($movie['recommendations']['results'], 0, 10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recommended): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $movieId = $recommended['id'] ?? 'unknown';
                $movieTitle = $recommended['title'] ?? 'Unknown';
                $posterPath = $recommended['poster_path'] ?? null;
                $releaseDate = $recommended['release_date'] ?? null;
                $rating = $recommended['vote_average'] ?? 0;
                $isCustom = $recommended['is_custom'] ?? false;
                $contentType = $recommended['content_type'] ?? 'custom';
                
                // Get image URL - handle both database and TMDB content
                $imageUrl = null;
                if ($posterPath) {
                    if (str_starts_with($posterPath, 'http')) {
                        $imageUrl = $posterPath;
                    } elseif (in_array($contentType, ['tmdb', 'article']) || str_starts_with($posterPath, '/')) {
                        // TMDB or Article content - use TMDB service
                        $imageUrl = app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w342');
                    } else {
                        // Database movie with custom image
                        $imageUrl = $posterPath;
                    }
                }
            ?>
            <a href="<?php echo e(route('movies.show', $movieId)); ?>" 
               class="group relative bg-white dark:!bg-bg-card rounded-xl overflow-hidden border border-gray-200 dark:!border-border-primary hover:border-accent/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-accent/20 cursor-pointer">
                <!-- Image Container -->
                <div class="relative overflow-hidden aspect-[2/3] bg-gray-100 dark:!bg-bg-card-hover">
                    <img src="<?php echo e($imageUrl ?? 'https://via.placeholder.com/300x450?text=No+Image'); ?>" 
                         alt="<?php echo e($movieTitle); ?>" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                         onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <!-- Rating Badge -->
                    <div class="absolute top-2 right-2 bg-black/80 dark:!bg-black/90 backdrop-blur-sm rounded-full px-2 py-1 flex items-center gap-1 border border-accent/30">
                        <span class="text-yellow-500 text-xs">★</span>
                        <span class="text-white text-xs font-bold"><?php echo e(number_format($rating, 1)); ?></span>
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
                        <?php echo e($movieTitle); ?>

                    </h3>
                    <div class="flex items-center justify-between">
                        <?php if($releaseDate): ?>
                        <span class="text-gray-600 dark:!text-text-secondary text-xs md:text-sm font-medium" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            <?php echo e(\Carbon\Carbon::parse($releaseDate)->format('Y')); ?>

                        </span>
                        <?php else: ?>
                        <span class="text-gray-600 dark:!text-text-secondary text-xs md:text-sm font-medium" style="font-family: 'Poppins', sans-serif; font-weight: 400;">N/A</span>
                        <?php endif; ?>
                        <div class="flex items-center gap-1.5 bg-gray-100 dark:!bg-bg-card-hover rounded-full px-2 py-1">
                            <span class="text-yellow-500 text-xs">★</span>
                            <span class="font-bold text-gray-900 dark:!text-white text-xs" style="font-family: 'Poppins', sans-serif; font-weight: 700;"><?php echo e(number_format($rating, 1)); ?></span>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
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

<?php if(isset($isCustom) && $isCustom && isset($content) && !empty($servers)): ?>
<script>
    function changeServer(videoUrl, buttonElement) {
        const iframe = document.getElementById('moviePlayer');
        if (iframe && videoUrl) {
            iframe.src = videoUrl;
            
            // Update active button styling
            if (buttonElement) {
                const allButtons = document.querySelectorAll('.server-btn');
                allButtons.forEach(btn => {
                    btn.classList.remove('bg-accent', 'text-white');
                    btn.classList.add('bg-gray-200', 'text-gray-700', 'dark:!bg-bg-card-hover', 'dark:!text-text-secondary');
                });
                
                buttonElement.classList.remove('bg-gray-200', 'text-gray-700', 'dark:!bg-bg-card-hover', 'dark:!text-text-secondary');
                buttonElement.classList.add('bg-accent', 'text-white');
            }
        }
    }
</script>
<?php endif; ?>

    <!-- Comments Section -->
    <?php if(isset($isCustom) && $isCustom && isset($content)): ?>
        <?php if (isset($component)) { $__componentOriginald04b9949d0dada8faa8863322f9b06a8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04b9949d0dada8faa8863322f9b06a8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.comments','data' => ['contentId' => $content->id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('comments'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['contentId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($content->id)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04b9949d0dada8faa8863322f9b06a8)): ?>
<?php $attributes = $__attributesOriginald04b9949d0dada8faa8863322f9b06a8; ?>
<?php unset($__attributesOriginald04b9949d0dada8faa8863322f9b06a8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04b9949d0dada8faa8863322f9b06a8)): ?>
<?php $component = $__componentOriginald04b9949d0dada8faa8863322f9b06a8; ?>
<?php unset($__componentOriginald04b9949d0dada8faa8863322f9b06a8); ?>
<?php endif; ?>
    <?php elseif(isset($content) && $content): ?>
        <?php if (isset($component)) { $__componentOriginald04b9949d0dada8faa8863322f9b06a8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04b9949d0dada8faa8863322f9b06a8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.comments','data' => ['contentId' => $content->id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('comments'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['contentId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($content->id)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04b9949d0dada8faa8863322f9b06a8)): ?>
<?php $attributes = $__attributesOriginald04b9949d0dada8faa8863322f9b06a8; ?>
<?php unset($__attributesOriginald04b9949d0dada8faa8863322f9b06a8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04b9949d0dada8faa8863322f9b06a8)): ?>
<?php $component = $__componentOriginald04b9949d0dada8faa8863322f9b06a8; ?>
<?php unset($__componentOriginald04b9949d0dada8faa8863322f9b06a8); ?>
<?php endif; ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\k\Desktop\Nazaarabox\resources\views/movies/show.blade.php ENDPATH**/ ?>