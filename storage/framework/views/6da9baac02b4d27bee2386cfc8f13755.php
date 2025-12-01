

<?php $__env->startSection('title', 'Cast Members - Nazaarabox'); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Section with Search -->
<section class="relative overflow-hidden mb-12" style="width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); background: linear-gradient(to bottom right, #1a1a1a, #0d0d0d, #000000);">
    <!-- Content -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="text-center">
            <!-- Main Heading -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6" style="font-family: 'Poppins', sans-serif; font-weight: 800; line-height: 1.2;">
                Discover Cast Members
            </h1>
            
            <!-- Subheading -->
            <p class="text-base md:text-lg text-gray-300 mb-10 max-w-2xl mx-auto leading-relaxed" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Browse through our collection of talented actors and actresses. Find your favorite stars and explore their filmography.
            </p>
            
            <!-- Search Form -->
            <form action="<?php echo e(route('cast.index')); ?>" method="GET" class="max-w-2xl mx-auto mb-8">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search for actors, actresses..." 
                        class="w-full px-6 py-4 pr-20 text-gray-800 bg-gray-200 border-0 rounded-full focus:outline-none focus:ring-2 focus:ring-accent/50 text-base transition-all duration-300"
                        style="font-family: 'Poppins', sans-serif; font-weight: 400;"
                        autocomplete="off"
                        value="<?php echo e($search); ?>">
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
                <a href="<?php echo e(route('movies.index')); ?>" class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-full transition-all duration-300 text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    Movies
                </a>
                <a href="<?php echo e(route('tv-shows.index')); ?>" class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-full transition-all duration-300 text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    TV Shows
                </a>
                <a href="<?php echo e(route('upcoming')); ?>" class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-full transition-all duration-300 text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    Upcoming
                </a>
                <a href="<?php echo e(route('completed')); ?>" class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-full transition-all duration-300 text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                    Completed
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bottom Wave -->
    <div class="absolute bottom-0 left-0 right-0 w-full wave-separator" style="pointer-events: none; z-index: 10;">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" class="w-full" style="height: 100px; display: block;">
            <defs>
                <!-- Beautiful shadow filter with glow for dark mode -->
                <filter id="castWaveShadowGlow" x="-50%" y="-150%" width="200%" height="400%">
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
            <path id="castWavePath" d="M0,120 L0,90 C120,70 240,50 360,60 C480,70 600,50 720,60 C840,70 960,50 1080,60 C1200,70 1320,50 1440,60 L1440,120 Z" fill="#FFFFFF"></path>
        </svg>
    </div>
    
    <style>
        /* Light mode: white wave */
        .wave-separator #castWavePath {
            fill: #FFFFFF;
            transition: fill 0.3s ease;
        }
        
        /* Dark mode: red wave with beautiful shadow and glow */
        html.dark .wave-separator #castWavePath {
            fill: #E50914;
            filter: url(#castWaveShadowGlow);
        }
        
        /* Additional CSS glow effect for extra beauty in dark mode */
        html.dark .wave-separator {
            filter: drop-shadow(0 -10px 30px rgba(229, 9, 20, 0.5)) drop-shadow(0 -5px 15px rgba(229, 9, 20, 0.3));
        }
    </style>
</section>

<div class="w-full px-4 sm:px-6 lg:px-8 xl:px-12 py-8">
    <!-- Cast Grid -->
    <?php if($casts->count() > 0): ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
        <?php $__currentLoopData = $casts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $profileUrl = null;
            $profilePath = $cast->profile_path ?? null;
            
            if ($profilePath) {
                if (str_starts_with($profilePath, 'http')) {
                    $profileUrl = $profilePath;
                } elseif (str_starts_with($profilePath, '/')) {
                    $profileUrl = app(\App\Services\TmdbService::class)->getImageUrl($profilePath, 'w185');
                } else {
                    $profileUrl = $profilePath;
                }
            }
        ?>
        <article class="group cursor-pointer">
            <a href="<?php echo e(route('cast.show', $cast->slug ?? $cast->id)); ?>" class="block">
                <div class="relative overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-800 aspect-[2/3] mb-3">
                    <?php if($profileUrl): ?>
                    <img src="<?php echo e($profileUrl); ?>" 
                         alt="<?php echo e($cast->name); ?>" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         style="display: block !important; visibility: visible !important; opacity: 1 !important;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-full h-full items-center justify-center hidden">
                        <span class="text-gray-400 text-xs">No Photo</span>
                    </div>
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="text-gray-400 text-xs">No Photo</span>
                    </div>
                    <?php endif; ?>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 dark:!text-white group-hover:text-accent transition-colors text-center line-clamp-2 mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    <?php echo e($cast->name); ?>

                </h3>
                <?php if($cast->contents_count > 0): ?>
                <p class="text-xs text-gray-500 dark:!text-text-secondary text-center" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    <?php echo e($cast->contents_count); ?> <?php echo e(Str::plural('Title', $cast->contents_count)); ?>

                </p>
                <?php endif; ?>
            </a>
        </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="text-center py-16">
        <p class="text-gray-600 dark:!text-text-secondary text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
            <?php if(!empty($search)): ?>
                No cast members found matching "<?php echo e($search); ?>".
            <?php else: ?>
                No cast members found.
            <?php endif; ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if($totalPages > 1): ?>
    <div class="mt-8 flex justify-center items-center gap-2 flex-wrap">
        <?php if($currentPage > 1): ?>
        <a href="<?php echo e(route('cast.index', array_merge(request()->query(), ['page' => $currentPage - 1]))); ?>" 
           class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 rounded-lg transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" 
           style="font-family: 'Poppins', sans-serif; font-weight: 500;">
            Previous
        </a>
        <?php endif; ?>
        
        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <?php if($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)): ?>
            <a href="<?php echo e(route('cast.index', array_merge(request()->query(), ['page' => $i]))); ?>" 
               class="px-4 py-2 rounded-lg transition-all <?php echo e($i == $currentPage ? 'bg-accent text-white' : 'bg-white hover:bg-gray-50 text-gray-900 dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white'); ?>" 
               style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                <?php echo e($i); ?>

            </a>
            <?php elseif($i == $currentPage - 3 || $i == $currentPage + 3): ?>
            <span class="px-2 text-gray-500">...</span>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if($currentPage < $totalPages): ?>
        <a href="<?php echo e(route('cast.index', array_merge(request()->query(), ['page' => $currentPage + 1]))); ?>" 
           class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 rounded-lg transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" 
           style="font-family: 'Poppins', sans-serif; font-weight: 500;">
            Next
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\k\Desktop\Nazaarabox\resources\views/cast/index.blade.php ENDPATH**/ ?>