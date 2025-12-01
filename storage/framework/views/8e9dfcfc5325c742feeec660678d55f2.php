
<?php
    // Ensure $pageSeo is always set to prevent null reference errors
    if (!isset($pageSeo) || $pageSeo === null) {
        $pageSeo = new \App\Models\PageSeo();
    }
?>


<div class="mb-6">
    <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
        Basic Information
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Page Key
            </label>
            <?php
                $pageKeyValue = old('page_key');
                if ($pageKeyValue === null) {
                    $pageKeyValue = $pageSeo->page_key ?? null;
                    if ($pageKeyValue === null) {
                        $pageKeyValue = $selectedPageKey ?? '';
                    }
                }
                $pageSeoId = $pageSeo->id ?? null;
                $isReadonly = (!empty($pageSeoId)) || !empty($selectedPageKey);
                $readonlyAttr = $isReadonly ? 'readonly' : 'required';
                $readonlyClass = $isReadonly ? 'bg-gray-100 dark:!bg-gray-800' : '';
            ?>
            <input type="text" name="page_key" value="<?php echo e($pageKeyValue); ?>" <?php echo e($readonlyAttr); ?> class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white <?php echo e($readonlyClass); ?>">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Page Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="page_name" value="<?php echo e(old('page_name', ($pageSeo->page_name ?? null) ?: '')); ?>" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
        </div>
    </div>
</div>


<div class="mb-6">
    <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
        Basic Meta Tags
    </h3>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Meta Title
            </label>
            <input type="text" name="meta_title" value="<?php echo e(old('meta_title', $pageSeo->meta_title ?? '')); ?>" maxlength="255"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Meta Description
            </label>
            <textarea name="meta_description" rows="3" maxlength="500"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white"><?php echo e(old('meta_description', $pageSeo->meta_description ?? '')); ?></textarea>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Meta Keywords
            </label>
            <input type="text" name="meta_keywords" value="<?php echo e(old('meta_keywords', $pageSeo->meta_keywords ?? '')); ?>" maxlength="500"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Meta Robots
            </label>
            <select name="meta_robots" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                <option value="index, follow" <?php echo e(old('meta_robots', $pageSeo->meta_robots ?? 'index, follow') === 'index, follow' ? 'selected' : ''); ?>>index, follow</option>
                <option value="noindex, follow" <?php echo e(old('meta_robots', $pageSeo->meta_robots ?? '') === 'noindex, follow' ? 'selected' : ''); ?>>noindex, follow</option>
                <option value="index, nofollow" <?php echo e(old('meta_robots', $pageSeo->meta_robots ?? '') === 'index, nofollow' ? 'selected' : ''); ?>>index, nofollow</option>
                <option value="noindex, nofollow" <?php echo e(old('meta_robots', $pageSeo->meta_robots ?? '') === 'noindex, nofollow' ? 'selected' : ''); ?>>noindex, nofollow</option>
            </select>
        </div>
    </div>
</div>


<div class="mb-6">
    <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
        Open Graph Tags
    </h3>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    OG Title
                </label>
                <input type="text" name="og_title" value="<?php echo e(old('og_title', $pageSeo->og_title ?? '')); ?>" maxlength="255"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    OG Type
                </label>
                <select name="og_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                    <option value="website" <?php echo e(old('og_type', $pageSeo->og_type ?? 'website') === 'website' ? 'selected' : ''); ?>>website</option>
                    <option value="article" <?php echo e(old('og_type', $pageSeo->og_type ?? '') === 'article' ? 'selected' : ''); ?>>article</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                OG Description
            </label>
            <textarea name="og_description" rows="3" maxlength="500"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white"><?php echo e(old('og_description', $pageSeo->og_description ?? '')); ?></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    OG Image URL
                </label>
                <input type="url" name="og_image" value="<?php echo e(old('og_image', $pageSeo->og_image ?? '')); ?>" maxlength="500"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    OG URL
                </label>
                <input type="url" name="og_url" value="<?php echo e(old('og_url', $pageSeo->og_url ?? '')); ?>" maxlength="500"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
            </div>
        </div>
    </div>
</div>


<div class="mb-6">
    <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
        Twitter Card Tags
    </h3>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Twitter Card Type
            </label>
            <select name="twitter_card" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                <option value="summary_large_image" <?php echo e(old('twitter_card', $pageSeo->twitter_card ?? 'summary_large_image') === 'summary_large_image' ? 'selected' : ''); ?>>summary_large_image</option>
                <option value="summary" <?php echo e(old('twitter_card', $pageSeo->twitter_card ?? '') === 'summary' ? 'selected' : ''); ?>>summary</option>
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Twitter Title
                </label>
                <input type="text" name="twitter_title" value="<?php echo e(old('twitter_title', $pageSeo->twitter_title ?? '')); ?>" maxlength="255"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Twitter Image URL
                </label>
                <input type="url" name="twitter_image" value="<?php echo e(old('twitter_image', $pageSeo->twitter_image ?? '')); ?>" maxlength="500"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Twitter Description
            </label>
            <textarea name="twitter_description" rows="2" maxlength="500"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white"><?php echo e(old('twitter_description', $pageSeo->twitter_description ?? '')); ?></textarea>
        </div>
    </div>
</div>


<div class="mb-6">
    <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
        Advanced SEO
    </h3>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Canonical URL
            </label>
            <input type="url" name="canonical_url" value="<?php echo e(old('canonical_url', $pageSeo->canonical_url ?? '')); ?>" maxlength="500"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Schema Markup (JSON-LD)
            </label>
            <?php
                $schemaMarkupValue = old('schema_markup');
                if (!$schemaMarkupValue && !empty($pageSeo->schema_markup)) {
                    $schemaMarkupValue = is_array($pageSeo->schema_markup) 
                        ? json_encode($pageSeo->schema_markup, JSON_PRETTY_PRINT) 
                        : $pageSeo->schema_markup;
                }
            ?>
            <textarea name="schema_markup" rows="6"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg font-mono text-sm focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white"
                      placeholder='Enter JSON-LD schema markup (e.g., {"context":"https://schema.org","type":"WebSite"})'><?php echo e($schemaMarkupValue ?? ''); ?></textarea>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Hreflang Tags (JSON)
            </label>
            <?php
                $hreflangTagsValue = old('hreflang_tags');
                if (!$hreflangTagsValue && !empty($pageSeo->hreflang_tags)) {
                    $hreflangTagsValue = is_array($pageSeo->hreflang_tags) 
                        ? json_encode($pageSeo->hreflang_tags, JSON_PRETTY_PRINT) 
                        : $pageSeo->hreflang_tags;
                }
            ?>
            <textarea name="hreflang_tags" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg font-mono text-sm focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white"
                      placeholder='{"en":"https://example.com","es":"https://example.com/es"}'><?php echo e($hreflangTagsValue ?? ''); ?></textarea>
        </div>
    </div>
</div>


<?php
    $defaultIsActive = isset($pageSeo) && isset($pageSeo->is_active) ? $pageSeo->is_active : true;
    $isActiveValue = old('is_active', $defaultIsActive);
    $checkedAttr = $isActiveValue ? 'checked' : '';
?>
<div class="mb-6">
    <label class="flex items-center gap-3">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" <?php echo e($checkedAttr); ?> class="rounded border-gray-300 text-accent focus:ring-accent">
        <span class="text-sm font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            Active (SEO will be applied when active)
        </span>
    </label>
</div>

<?php /**PATH C:\Users\k\Desktop\Nazaarabox\resources\views/admin/page-seo/_form.blade.php ENDPATH**/ ?>