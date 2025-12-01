<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['items']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['items']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Ensure items is an array
    if (!is_array($items)) {
        $items = [];
    }
    
    // Always start with Home
    $breadcrumbs = [
        [
            'label' => 'Home',
            'url' => route('home'),
            'position' => 1
        ]
    ];
    
    // Add custom items
    $position = 2;
    foreach ($items as $item) {
        if (isset($item['label'])) {
            $breadcrumbs[] = [
                'label' => $item['label'],
                'url' => $item['url'] ?? null,
                'position' => $position++
            ];
        }
    }
?>

<?php if(count($breadcrumbs) > 1): ?>
<nav aria-label="Breadcrumb" class="mb-6">
    <ol class="flex flex-wrap items-center gap-2 text-sm" itemscope itemtype="https://schema.org/BreadcrumbList" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
        <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="flex items-center" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <?php if($crumb['url'] && !$loop->last): ?>
                <a href="<?php echo e($crumb['url']); ?>" 
                   class="text-gray-600 dark:!text-text-secondary hover:text-accent transition-colors" 
                   itemprop="item">
                    <span itemprop="name"><?php echo e($crumb['label']); ?></span>
                </a>
                <meta itemprop="position" content="<?php echo e($crumb['position']); ?>">
            <?php else: ?>
                <span class="text-gray-900 dark:!text-white font-semibold" itemprop="name"><?php echo e($crumb['label']); ?></span>
                <meta itemprop="position" content="<?php echo e($crumb['position']); ?>">
            <?php endif; ?>
            
            <?php if(!$loop->last): ?>
                <svg class="w-4 h-4 mx-2 text-gray-400 dark:!text-text-tertiary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            <?php endif; ?>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ol>
</nav>
<?php endif; ?>

<?php /**PATH C:\Users\k\Desktop\Nazaarabox\resources\views/components/breadcrumbs.blade.php ENDPATH**/ ?>