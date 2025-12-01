

<?php $__env->startSection('title', 'Admin - Content Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-gray-600 hover:text-accent dark:!text-text-secondary dark:!hover:text-accent transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    ← Dashboard
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Content Management
            </h1>
            <p class="text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Manage movies, TV shows, and other content
            </p>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Dashboard
            </a>
            <a href="<?php echo e(route('admin.contents.create')); ?>" class="px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Add New Content
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg dark:!bg-green-900/20 dark:!border-green-700 dark:!text-green-400">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg dark:!bg-red-900/20 dark:!border-red-700 dark:!text-red-400">
        <?php echo e(session('error')); ?>

    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="mb-6 bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-4">
        <form method="GET" action="<?php echo e(route('admin.contents.index')); ?>" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Search</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search by title..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
            </div>
            <div class="min-w-[150px]">
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Type</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                    <option value="">All Types</option>
                    <?php $__currentLoopData = \App\Models\Content::getContentTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type); ?>" <?php echo e(request('type') === $type ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                    <option value="">All Status</option>
                    <option value="published" <?php echo e(request('status') === 'published' ? 'selected' : ''); ?>>Published</option>
                    <option value="draft" <?php echo e(request('status') === 'draft' ? 'selected' : ''); ?>>Draft</option>
                    <option value="upcoming" <?php echo e(request('status') === 'upcoming' ? 'selected' : ''); ?>>Upcoming</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <?php if($contents->count() > 0): ?>
    <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:!bg-bg-card-hover">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Poster</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Servers</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:!divide-border-secondary">
                    <?php $__currentLoopData = $contents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 dark:!hover:bg-bg-card-hover">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-16 h-24 rounded overflow-hidden bg-gray-100 dark:!bg-bg-card-hover">
                                <?php if($content->poster_path): ?>
                                    <?php if(str_starts_with($content->poster_path, 'http')): ?>
                                        <img src="<?php echo e($content->poster_path); ?>" alt="<?php echo e($content->title); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?php
                                            $contentType = $content->content_type ?? 'custom';
                                            if (in_array($contentType, ['tmdb', 'article']) || str_starts_with($content->poster_path, '/')) {
                                                $posterUrl = app(\App\Services\TmdbService::class)->getImageUrl($content->poster_path, 'w185');
                                            } else {
                                                // For custom content, use the path directly (not from storage)
                                                $posterUrl = $content->poster_path;
                                            }
                                        ?>
                                        <img src="<?php echo e($posterUrl); ?>" alt="<?php echo e($content->title); ?>" class="w-full h-full object-cover">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Image</div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                <?php echo e($content->title); ?>

                            </div>
                            <div class="text-xs text-gray-500 dark:!text-text-tertiary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                <?php if(in_array($content->content_type ?? 'custom', ['tmdb', 'article'])): ?>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded dark:!bg-blue-900/20 dark:!text-blue-400"><?php echo e($content->content_type === 'article' ? 'Article' : 'TMDB'); ?></span>
                                    <?php if($content->tmdb_id): ?> ID: <?php echo e($content->tmdb_id); ?> <?php endif; ?>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded dark:!bg-gray-800 dark:!text-gray-400">Custom</span>
                                <?php endif; ?>
                            </div>
                            <?php if($content->description): ?>
                            <div class="text-xs text-gray-500 dark:!text-text-tertiary mt-1 line-clamp-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                <?php echo e(Str::limit($content->description, 80)); ?>

                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                <?php echo e(ucfirst(str_replace('_', ' ', $content->type))); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($content->status === 'published'): ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:!bg-green-900/20 dark:!text-green-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Published</span>
                            <?php elseif($content->status === 'draft'): ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:!bg-gray-800 dark:!text-gray-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Draft</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:!bg-yellow-900/20 dark:!text-yellow-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Upcoming</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                <?php echo e(is_array($content->servers) ? count($content->servers) : 0); ?> server(s)
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            <?php echo e($content->created_at->format('M d, Y')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('admin.contents.show', $content)); ?>" class="text-blue-600 hover:text-blue-900 dark:!text-blue-400 dark:!hover:text-blue-300" style="font-family: 'Poppins', sans-serif; font-weight: 600;">View</a>
                                <a href="<?php echo e(route('admin.contents.edit', $content)); ?>" class="text-indigo-600 hover:text-indigo-900 dark:!text-indigo-400 dark:!hover:text-indigo-300" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Edit</a>
                                <?php if(in_array($content->type, ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show'])): ?>
                                    <a href="<?php echo e(route('admin.episodes.index', $content)); ?>" class="text-purple-600 hover:text-purple-900 dark:!text-purple-400 dark:!hover:text-purple-300" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Episodes</a>
                                <?php endif; ?>
                                <form action="<?php echo e(route('admin.contents.destroy', $content)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this content?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:!text-red-400 dark:!hover:text-red-300" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        <?php echo e($contents->links()); ?>

    </div>
    <?php else: ?>
    <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-12 text-center">
        <p class="text-gray-600 dark:!text-text-secondary text-lg mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 400;">No content found.</p>
        <a href="<?php echo e(route('admin.contents.create')); ?>" class="inline-block px-6 py-3 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            Add New Content
        </a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\k\Desktop\Nazaarabox\resources\views/admin/contents/index.blade.php ENDPATH**/ ?>