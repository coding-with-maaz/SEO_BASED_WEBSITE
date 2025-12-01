

<?php $__env->startSection('title', '404 - Page Not Found'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-[60vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-2xl mx-auto text-center">
        <!-- Error Code -->
        <div class="mb-8">
            <h1 class="text-9xl md:text-[12rem] font-bold text-accent leading-none" style="font-family: 'Poppins', sans-serif; font-weight: 800;">
                404
            </h1>
        </div>
        
        <!-- Error Message -->
        <div class="mb-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Page Not Found
            </h2>
            <p class="text-lg md:text-xl text-gray-600 dark:!text-text-secondary mb-6" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Oops! The page you're looking for doesn't exist or has been moved.
            </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="<?php echo e(route('home')); ?>" 
               class="inline-flex items-center px-6 py-3 bg-accent hover:bg-accent-light text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
               style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Go to Homepage
            </a>
            <a href="<?php echo e(route('movies.index')); ?>" 
               class="inline-flex items-center px-6 py-3 bg-white dark:!bg-bg-card border-2 border-gray-300 dark:!border-border-primary hover:border-accent text-gray-700 dark:!text-white hover:text-accent font-semibold rounded-lg transition-all duration-300 transform hover:scale-105"
               style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
                </svg>
                Browse Movies
            </a>
        </div>
        
        <!-- Helpful Links -->
        <div class="mt-12 pt-8 border-t border-gray-200 dark:!border-border-primary">
            <p class="text-sm text-gray-500 dark:!text-text-tertiary mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                You might be looking for:
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="<?php echo e(route('tv-shows.index')); ?>" class="text-accent hover:text-accent-light font-semibold text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 600;">TV Shows</a>
                <a href="<?php echo e(route('cast.index')); ?>" class="text-accent hover:text-accent-light font-semibold text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Cast</a>
                <a href="<?php echo e(route('upcoming')); ?>" class="text-accent hover:text-accent-light font-semibold text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Upcoming</a>
                <a href="<?php echo e(route('completed')); ?>" class="text-accent hover:text-accent-light font-semibold text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Completed</a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\k\Desktop\Nazaarabox\resources\views/errors/404.blade.php ENDPATH**/ ?>