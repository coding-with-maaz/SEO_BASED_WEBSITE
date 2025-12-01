

<?php $__env->startSection('title', ($cast->name ?? 'Cast Member') . ' - Nazaarabox'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $profileUrl = null;
    $profilePath = $cast->profile_path ?? null;
    
    if ($profilePath) {
        if (str_starts_with($profilePath, 'http')) {
            $profileUrl = $profilePath;
        } elseif (str_starts_with($profilePath, '/')) {
            $profileUrl = app(\App\Services\TmdbService::class)->getImageUrl($profilePath, 'w500');
        } else {
            $profileUrl = $profilePath;
        }
    }
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
    <!-- Header Section with Back Button -->
    <div class="mb-6">
        <a href="<?php echo e(route('cast.index')); ?>" class="inline-flex items-center gap-2 text-gray-600 dark:!text-text-secondary hover:text-accent transition-colors mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Cast
        </a>
    </div>

    <!-- Main Info Section -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 md:gap-8 mb-8 md:mb-12">
        <!-- Profile Photo -->
        <div class="lg:col-span-1 order-2 lg:order-1">
            <div class="sticky top-24 max-w-xs mx-auto lg:max-w-full">
                <?php if($profileUrl): ?>
                <img src="<?php echo e($profileUrl); ?>" 
                     alt="<?php echo e($cast->name); ?>" 
                     class="w-full max-w-[280px] mx-auto rounded-xl shadow-2xl"
                     style="display: block !important; visibility: visible !important; opacity: 1 !important;"
                     onerror="this.src='https://via.placeholder.com/500x750?text=No+Image'">
                <?php else: ?>
                <div class="w-full aspect-[2/3] max-w-[280px] mx-auto bg-gray-200 dark:bg-gray-800 rounded-xl shadow-2xl flex items-center justify-center">
                    <span class="text-gray-400 text-lg">No Photo</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Details -->
        <div class="lg:col-span-4 order-1 lg:order-2">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:!text-white mb-4 md:mb-6" style="font-family: 'Poppins', sans-serif; font-weight: 800; line-height: 1.2;">
                <?php echo e($cast->name); ?>

            </h1>
            
            <!-- Personal Information Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <?php if($cast->birthday): ?>
                <div class="bg-gray-100 dark:!bg-bg-card rounded-lg p-4">
                    <p class="text-xs text-gray-500 dark:!text-text-secondary mb-1 uppercase tracking-wide" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Birthday</p>
                    <p class="text-lg font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <?php echo e($cast->birthday->format('F d, Y')); ?>

                    </p>
                </div>
                <?php endif; ?>
                
                <?php if($cast->birthplace): ?>
                <div class="bg-gray-100 dark:!bg-bg-card rounded-lg p-4">
                    <p class="text-xs text-gray-500 dark:!text-text-secondary mb-1 uppercase tracking-wide" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Birthplace</p>
                    <p class="text-lg font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <?php echo e($cast->birthplace); ?>

                    </p>
                </div>
                <?php endif; ?>
                
                <?php if($movies->count() > 0 || $tvShows->count() > 0): ?>
                <div class="bg-gray-100 dark:!bg-bg-card rounded-lg p-4 sm:col-span-2">
                    <p class="text-xs text-gray-500 dark:!text-text-secondary mb-1 uppercase tracking-wide" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Known For</p>
                    <p class="text-lg font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <?php echo e($movies->count() + $tvShows->count()); ?> <?php echo e(Str::plural('Title', $movies->count() + $tvShows->count())); ?>

                        <?php if($movies->count() > 0 && $tvShows->count() > 0): ?>
                        <span class="text-sm font-normal text-gray-600 dark:!text-text-secondary">(<?php echo e($movies->count()); ?> Movie<?php echo e($movies->count() !== 1 ? 's' : ''); ?>, <?php echo e($tvShows->count()); ?> TV Show<?php echo e($tvShows->count() !== 1 ? 's' : ''); ?>)</span>
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Biography -->
            <?php if($cast->biography): ?>
            <div class="mb-6">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:!text-white mb-3 md:mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Biography</h2>
                <div class="prose prose-sm md:prose-base max-w-none">
                    <p class="text-gray-700 dark:!text-text-secondary leading-relaxed text-sm md:text-base" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        <?php echo e($cast->biography); ?>

                    </p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Movies Section -->
    <?php if($movies->count() > 0): ?>
    <div class="mb-8 md:mb-12">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Movies
            </h2>
            <span class="text-sm text-gray-500 dark:!text-text-secondary bg-gray-100 dark:!bg-bg-card px-3 py-1 rounded-full" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                <?php echo e($movies->count()); ?>

            </span>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 2xl:grid-cols-8 gap-3 md:gap-4">
            <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $posterPath = $movie->poster_path ?? null;
                $posterUrl = null;
                
                if ($posterPath) {
                    if (str_starts_with($posterPath, 'http')) {
                        $posterUrl = $posterPath;
                    } elseif (str_starts_with($posterPath, '/') || ($movie->content_type ?? 'custom') === 'tmdb') {
                        $posterUrl = app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w185');
                    } else {
                        $posterUrl = $posterPath;
                    }
                }
                
                // Get content type and dubbing language
                $contentTypes = \App\Models\Content::getContentTypes();
                $contentTypeKey = $movie->type ?? 'movie';
                $contentTypeName = $contentTypes[$contentTypeKey] ?? ucfirst(str_replace('_', ' ', $contentTypeKey));
                $dubbingLanguage = $movie->dubbing_language ?? null;
                
                $character = $movie->pivot->character ?? '';
                $routeName = 'movies.show';
                $itemId = $movie->slug ?? ('custom_' . $movie->id);
            ?>
            <article class="group cursor-pointer">
                <a href="<?php echo e(route($routeName, $itemId)); ?>" class="block">
                    <div class="relative overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-800 aspect-[2/3]" style="background-color: transparent !important;">
                        <?php if($posterUrl): ?>
                        <img src="<?php echo e($posterUrl); ?>" 
                             alt="<?php echo e($movie->title); ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative; z-index: 1;"
                             onerror="this.src='https://via.placeholder.com/185x278?text=No+Image'">
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center" style="position: relative; z-index: 1;">
                            <span class="text-gray-400 text-xs">No Image</span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Content Type Badge - Top Left -->
                        <?php if(!empty($contentTypeName)): ?>
                        <div class="absolute top-1.5 left-1.5 bg-accent text-white px-2 py-0.5 rounded-full text-[10px] font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600; z-index: 3; backdrop-filter: blur(4px); background-color: rgba(229, 9, 20, 0.9);">
                            <?php echo e($contentTypeName); ?>

                        </div>
                        <?php endif; ?>
                        
                        <!-- Dubbing Language Badge - Top Right -->
                        <?php if(!empty($dubbingLanguage)): ?>
                        <div class="absolute top-1.5 right-1.5 bg-blue-600 text-white px-2 py-0.5 rounded-full text-[10px] font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600; z-index: 3; backdrop-filter: blur(4px); background-color: rgba(37, 99, 235, 0.9);">
                            <?php echo e(ucfirst($dubbingLanguage)); ?>

                        </div>
                        <?php endif; ?>
                        
                        <!-- Title Overlay with Character - Always Visible -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex items-end pointer-events-none" style="z-index: 2;">
                            <div class="w-full p-2 pointer-events-auto">
                                <h3 class="text-[10px] font-bold text-white mb-0.5 line-clamp-2" style="font-family: 'Poppins', sans-serif; font-weight: 800; text-shadow: 0 2px 8px rgba(0,0,0,0.9);">
                                    <?php echo e($movie->title); ?>

                                </h3>
                                <?php if($character): ?>
                                <p class="text-[9px] text-gray-200 line-clamp-1" style="font-family: 'Poppins', sans-serif; font-weight: 500; text-shadow: 0 1px 4px rgba(0,0,0,0.8);">
                                    as <?php echo e($character); ?>

                                </p>
                                <?php endif; ?>
                                <?php if($movie->release_date): ?>
                                <p class="text-[9px] text-gray-300 mt-0.5" style="font-family: 'Poppins', sans-serif; font-weight: 400; text-shadow: 0 1px 4px rgba(0,0,0,0.8);">
                                    <?php echo e($movie->release_date->format('Y')); ?>

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
    
    <!-- TV Shows Section -->
    <?php if($tvShows->count() > 0): ?>
    <div class="mb-8 md:mb-12">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                TV Shows & Series
            </h2>
            <span class="text-sm text-gray-500 dark:!text-text-secondary bg-gray-100 dark:!bg-bg-card px-3 py-1 rounded-full" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                <?php echo e($tvShows->count()); ?>

            </span>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 2xl:grid-cols-8 gap-3 md:gap-4">
            <?php $__currentLoopData = $tvShows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tvShow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $posterPath = $tvShow->poster_path ?? null;
                $posterUrl = null;
                
                if ($posterPath) {
                    if (str_starts_with($posterPath, 'http')) {
                        $posterUrl = $posterPath;
                    } elseif (str_starts_with($posterPath, '/') || ($tvShow->content_type ?? 'custom') === 'tmdb') {
                        $posterUrl = app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w185');
                    } else {
                        $posterUrl = $posterPath;
                    }
                }
                
                // Get content type and dubbing language
                $contentTypes = \App\Models\Content::getContentTypes();
                $contentTypeKey = $tvShow->type ?? 'tv_show';
                $contentTypeName = $contentTypes[$contentTypeKey] ?? ucfirst(str_replace('_', ' ', $contentTypeKey));
                $dubbingLanguage = $tvShow->dubbing_language ?? null;
                
                $character = $tvShow->pivot->character ?? '';
                $routeName = 'tv-shows.show';
                $itemId = $tvShow->slug ?? ('custom_' . $tvShow->id);
            ?>
            <article class="group cursor-pointer">
                <a href="<?php echo e(route($routeName, $itemId)); ?>" class="block">
                    <div class="relative overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-800 aspect-[2/3]" style="background-color: transparent !important;">
                        <?php if($posterUrl): ?>
                        <img src="<?php echo e($posterUrl); ?>" 
                             alt="<?php echo e($tvShow->title); ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative; z-index: 1;"
                             onerror="this.src='https://via.placeholder.com/185x278?text=No+Image'">
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center" style="position: relative; z-index: 1;">
                            <span class="text-gray-400 text-xs">No Image</span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Content Type Badge - Top Left -->
                        <?php if(!empty($contentTypeName)): ?>
                        <div class="absolute top-1.5 left-1.5 bg-accent text-white px-2 py-0.5 rounded-full text-[10px] font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600; z-index: 3; backdrop-filter: blur(4px); background-color: rgba(229, 9, 20, 0.9);">
                            <?php echo e($contentTypeName); ?>

                        </div>
                        <?php endif; ?>
                        
                        <!-- Dubbing Language Badge - Top Right -->
                        <?php if(!empty($dubbingLanguage)): ?>
                        <div class="absolute top-1.5 right-1.5 bg-blue-600 text-white px-2 py-0.5 rounded-full text-[10px] font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600; z-index: 3; backdrop-filter: blur(4px); background-color: rgba(37, 99, 235, 0.9);">
                            <?php echo e(ucfirst($dubbingLanguage)); ?>

                        </div>
                        <?php endif; ?>
                        
                        <!-- Title Overlay with Character - Always Visible -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex items-end pointer-events-none" style="z-index: 2;">
                            <div class="w-full p-2 pointer-events-auto">
                                <h3 class="text-[10px] font-bold text-white mb-0.5 line-clamp-2" style="font-family: 'Poppins', sans-serif; font-weight: 800; text-shadow: 0 2px 8px rgba(0,0,0,0.9);">
                                    <?php echo e($tvShow->title); ?>

                                </h3>
                                <?php if($character): ?>
                                <p class="text-[9px] text-gray-200 line-clamp-1" style="font-family: 'Poppins', sans-serif; font-weight: 500; text-shadow: 0 1px 4px rgba(0,0,0,0.8);">
                                    as <?php echo e($character); ?>

                                </p>
                                <?php endif; ?>
                                <?php if($tvShow->release_date): ?>
                                <p class="text-[9px] text-gray-300 mt-0.5" style="font-family: 'Poppins', sans-serif; font-weight: 400; text-shadow: 0 1px 4px rgba(0,0,0,0.8);">
                                    <?php echo e($tvShow->release_date->format('Y')); ?>

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
    
    <?php if($movies->count() === 0 && $tvShows->count() === 0): ?>
    <div class="text-center py-12 md:py-16">
        <p class="text-gray-600 dark:!text-text-secondary text-base md:text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
            No movies or TV shows available for this cast member.
        </p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\k\Desktop\Nazaarabox\resources\views/cast/show.blade.php ENDPATH**/ ?>