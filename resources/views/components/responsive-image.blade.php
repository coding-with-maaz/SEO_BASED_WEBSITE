@php
    /**
     * Responsive Image Component
     * 
     * Generates optimized responsive images with srcset and lazy loading
     * 
     * @param string $path - Image path from TMDB or custom URL
     * @param string $alt - Alt text for the image
     * @param string $type - Image type: 'backdrop' (16:9) or 'poster' (2:3) or 'profile'
     * @param string $contentType - Content type: 'tmdb', 'article', or 'custom'
     * @param bool $lazy - Enable lazy loading (default: true for images below fold)
     * @param string $class - Additional CSS classes
     * @param array $attributes - Additional HTML attributes
     */
    
    $tmdbService = app(\App\Services\TmdbService::class);
    $imageUrl = null;
    $srcset = '';
    $sizes = '';
    
    // Determine if it's a TMDB image
    $isTmdbImage = false;
    if ($path) {
        if (str_starts_with($path, 'http')) {
            // Full URL - use directly (custom image)
            $imageUrl = $path;
        } elseif (in_array($contentType ?? 'custom', ['tmdb', 'article']) || str_starts_with($path, '/')) {
            // TMDB/Article content
            $isTmdbImage = true;
        } else {
            // Custom content - use path directly
            $imageUrl = $path;
        }
    }
    
    // Generate responsive image URLs for TMDB images
    if ($isTmdbImage && $path) {
        if ($type === 'backdrop') {
            // Backdrop images (16:9 aspect ratio)
            // Sizes: w300 (300x169), w780 (780x439), w1280 (1280x720)
            $baseUrl = rtrim($tmdbService->getImageUrl($path, 'w300'), '/w300');
            $imageUrl = $baseUrl . '/w780/' . ltrim($path, '/');
            $srcset = $baseUrl . '/w300/' . ltrim($path, '/') . ' 300w, ' .
                     $baseUrl . '/w780/' . ltrim($path, '/') . ' 780w, ' .
                     $baseUrl . '/w1280/' . ltrim($path, '/') . ' 1280w';
            $sizes = '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 665px';
        } elseif ($type === 'poster') {
            // Poster images (2:3 aspect ratio) - for small thumbnails
            // Sizes: w92, w154, w185, w342, w500
            $baseUrl = rtrim($tmdbService->getImageUrl($path, 'w185'), '/w185');
            $imageUrl = $baseUrl . '/w185/' . ltrim($path, '/');
            $srcset = $baseUrl . '/w92/' . ltrim($path, '/') . ' 92w, ' .
                     $baseUrl . '/w154/' . ltrim($path, '/') . ' 154w, ' .
                     $baseUrl . '/w185/' . ltrim($path, '/') . ' 185w';
            $sizes = '(max-width: 640px) 112px, 168px';
        } elseif ($type === 'poster-large') {
            // Large poster images (2:3 aspect ratio)
            $baseUrl = rtrim($tmdbService->getImageUrl($path, 'w300'), '/w300');
            $imageUrl = $baseUrl . '/w300/' . ltrim($path, '/');
            $srcset = $baseUrl . '/w185/' . ltrim($path, '/') . ' 185w, ' .
                     $baseUrl . '/w300/' . ltrim($path, '/') . ' 300w, ' .
                     $baseUrl . '/w500/' . ltrim($path, '/') . ' 500w';
            $sizes = '(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 300px';
        } else {
            // Default: use w500
            $imageUrl = $tmdbService->getImageUrl($path, 'w500');
        }
    } elseif (!$imageUrl && $path) {
        // Fallback for custom images
        $imageUrl = $path;
    }
    
    // Default placeholder
    $placeholder = $type === 'backdrop' 
        ? 'https://via.placeholder.com/780x439?text=No+Image'
        : ($type === 'poster' || $type === 'poster-large'
            ? 'https://via.placeholder.com/300x450?text=No+Image'
            : 'https://via.placeholder.com/500x281?text=No+Image');
    
    $finalImageUrl = $imageUrl ?? $placeholder;
    
    // Build attributes
    $imgAttributes = array_merge([
        'src' => $finalImageUrl,
        'alt' => $alt ?? 'Image',
        'class' => $class ?? '',
        'onerror' => "this.src='{$placeholder}'",
    ], $attributes ?? []);
    
    // Add srcset and sizes for responsive images
    if ($srcset) {
        $imgAttributes['srcset'] = $srcset;
        $imgAttributes['sizes'] = $sizes;
    }
    
    // Add lazy loading
    if ($lazy ?? true) {
        $imgAttributes['loading'] = 'lazy';
        $imgAttributes['decoding'] = 'async';
    }
    
    // Build style attribute if provided
    $styleAttr = '';
    if (isset($style)) {
        $styleAttr = ' style="' . $style . '"';
    }
@endphp

<img {!! collect($imgAttributes)->map(function($value, $key) {
    return $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
})->implode(' ') !!}{!! $styleAttr !!}>

