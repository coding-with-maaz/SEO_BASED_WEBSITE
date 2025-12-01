<!DOCTYPE html>
<html lang="{{ $seo['locale'] ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        // ALWAYS prioritize PageSeo from database - ignore controller SEO if PageSeo exists
        $seoService = app(\App\Services\SeoService::class);
        
        // Detect page key from route
        $routeName = request()->route()?->getName();
        $pageKeyMap = [
            'home' => 'home',
            'movies.index' => 'movies.index',
            'tv-shows.index' => 'tv-shows.index',
            'cast.index' => 'cast.index',
            'search' => 'search',
            'about' => 'about',
            'dmca' => 'dmca',
            'completed' => 'completed',
            'upcoming' => 'upcoming',
            'how-to-download' => 'how-to-download',
        ];
        
        $detectedPageKey = $pageKeyMap[$routeName] ?? null;
        
        // ALWAYS check PageSeo first - this overrides controller SEO
        if ($detectedPageKey) {
            // Use the model method to get fresh PageSeo data
            $pageSeo = \App\Models\PageSeo::getByPageKey($detectedPageKey);
            
            if ($pageSeo) {
                // PageSeo exists and is active - ALWAYS use it (overrides controller SEO)
                // Pass empty array to ensure PageSeo data takes priority
                $seo = $seoService->generate([], $detectedPageKey);
            } else {
                // No active PageSeo - use controller SEO or auto-detect
                $seo = $seo ?? $seoService->forCurrentRoute();
            }
        } else {
            // Unknown route - use controller SEO or auto-detect
            $seo = $seo ?? $seoService->forCurrentRoute();
        }
    @endphp
    
    <!-- Primary Meta Tags -->
    <title>{{ $seo['title'] ?? 'Nazaarabox - Movies & TV Shows' }}</title>
    <meta name="title" content="{{ $seo['title'] ?? 'Nazaarabox - Movies & TV Shows' }}">
    <meta name="description" content="{{ $seo['description'] ?? 'Watch and download your favorite movies and TV shows. Browse thousands of titles in high quality.' }}">
    <meta name="keywords" content="{{ $seo['keywords'] ?? 'movies, tv shows, streaming, download, watch online, entertainment' }}">
    <meta name="author" content="{{ $seo['author'] ?? 'Nazaarabox' }}">
    <meta name="robots" content="{{ $seo['robots'] ?? 'index, follow' }}">
    <meta name="language" content="{{ $seo['locale'] ?? 'en' }}">
    <meta name="revisit-after" content="7 days">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
    <meta property="og:url" content="{{ $seo['url'] ?? url()->current() }}">
    <meta property="og:title" content="{{ $seo['og_title'] ?? $seo['title'] ?? 'Nazaarabox - Movies & TV Shows' }}">
    <meta property="og:description" content="{{ $seo['og_description'] ?? $seo['description'] ?? 'Watch and download your favorite movies and TV shows. Browse thousands of titles in high quality.' }}">
    <meta property="og:image" content="{{ $seo['og_image'] ?? $seo['image'] ?? asset('favicon.ico') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $seo['title'] ?? 'Nazaarabox' }}">
    <meta property="og:site_name" content="Nazaarabox">
    <meta property="og:locale" content="{{ $seo['locale'] ?? 'en_US' }}">
    @if(!empty($seo['published_time']))
    <meta property="og:published_time" content="{{ $seo['published_time'] }}">
    @endif
    @if(!empty($seo['modified_time']))
    <meta property="og:modified_time" content="{{ $seo['modified_time'] }}">
    @endif
    @if($seoService->getFacebookAppId())
    <meta property="fb:app_id" content="{{ $seoService->getFacebookAppId() }}">
    @endif
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="{{ $seo['twitter_card'] ?? 'summary_large_image' }}">
    <meta name="twitter:url" content="{{ $seo['url'] ?? url()->current() }}">
    <meta name="twitter:title" content="{{ $seo['twitter_title'] ?? $seo['title'] ?? 'Nazaarabox - Movies & TV Shows' }}">
    <meta name="twitter:description" content="{{ $seo['twitter_description'] ?? $seo['description'] ?? 'Watch and download your favorite movies and TV shows. Browse thousands of titles in high quality.' }}">
    <meta name="twitter:image" content="{{ $seo['twitter_image'] ?? $seo['image'] ?? asset('favicon.ico') }}">
    <meta name="twitter:image:alt" content="{{ $seo['twitter_title'] ?? $seo['title'] ?? 'Nazaarabox' }}">
    @if($seoService->getTwitterHandle())
    <meta name="twitter:site" content="{{ $seoService->getTwitterHandle() }}">
    <meta name="twitter:creator" content="{{ $seoService->getTwitterHandle() }}">
    @endif
    
    <!-- Additional SEO Enhancements -->
    <meta name="theme-color" content="#E50914">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">
    <meta name="application-name" content="Nazaarabox">
    <meta name="msapplication-TileColor" content="#E50914">
    <meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">
    
    <!-- Alternate Languages (Hreflang) -->
    @if(!empty($seo['alternate_locales']))
        @foreach($seo['alternate_locales'] as $locale => $url)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ $url }}">
        @endforeach
    @endif
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">
    
    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://image.tmdb.org">
    <link rel="dns-prefetch" href="https://api.themoviedb.org">
    
    <!-- Structured Data (JSON-LD) -->
    @if(!empty($seo['schema']))
        @foreach($seo['schema'] as $schema)
        <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
        @endforeach
    @endif
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Poppins', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
                    },
                    colors: {
                        'bg-primary': '#0D0D0D',
                        'bg-secondary': '#181818',
                        'bg-card': '#1F1F1F',
                        'bg-card-hover': '#2A2A2A',
                        'accent': '#E50914',
                        'accent-dark': '#B20710',
                        'accent-light': '#F40612',
                        'text-primary': '#FFFFFF',
                        'text-secondary': '#B3B3B3',
                        'text-tertiary': '#808080',
                        'text-muted': '#666666',
                        'border-primary': 'rgba(255, 255, 255, 0.1)',
                        'border-secondary': 'rgba(255, 255, 255, 0.05)',
                        'rating': '#FFD700',
                    },
                    backgroundImage: {
                        'gradient-primary': 'linear-gradient(135deg, #E50914 0%, #B20710 100%)',
                        'gradient-overlay': 'linear-gradient(180deg, rgba(13, 13, 13, 0.3) 0%, rgba(13, 13, 13, 0.9) 100%)',
                    },
                    boxShadow: {
                        'accent': '0 10px 30px rgba(229, 9, 20, 0.3)',
                        'accent-lg': '0 10px 30px rgba(229, 9, 20, 0.4)',
                        'card': '0 4px 12px rgba(0, 0, 0, 0.3)',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <style>
        :root {
            /* ============================================
               DARK NEUTRAL + RED ACCENT THEME
               Professional theme optimized for movie/TV content
               ============================================ */
            
            /* Primary Background */
            --bg-primary: #0D0D0D;
            
            /* Secondary Background */
            --bg-secondary: #181818;
            
            /* Cards/Containers */
            --bg-card: #1F1F1F;
            --bg-card-hover: #2A2A2A;
            
            /* Accent Color (Buttons/Links) - Netflix Red */
            --color-accent: #E50914;
            --color-accent-dark: #B20710;
            --color-accent-light: #F40612;
            --color-accent-hover: #F40612;
            
            /* Text Colors */
            --text-primary: #FFFFFF;
            --text-secondary: #B3B3B3;
            --text-tertiary: #808080;
            --text-muted: #666666;
            --text-disabled: #4D4D4D;
            
            /* Background Overlays */
            --bg-overlay: rgba(13, 13, 13, 0.95);
            --bg-overlay-light: rgba(31, 31, 31, 0.8);
            --bg-overlay-medium: rgba(31, 31, 31, 0.9);
            --bg-overlay-dark: rgba(13, 13, 13, 0.98);
            --bg-input: rgba(31, 31, 31, 0.8);
            
            /* Border Colors */
            --border-primary: rgba(255, 255, 255, 0.1);
            --border-secondary: rgba(255, 255, 255, 0.05);
            --border-accent: rgba(229, 9, 20, 0.3);
            --border-focus: rgba(229, 9, 20, 0.5);
            
            /* Gradient Colors */
            --gradient-primary: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent-dark) 100%);
            --gradient-primary-reverse: linear-gradient(135deg, var(--color-accent-dark) 0%, var(--color-accent) 100%);
            --gradient-background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
            --gradient-overlay: linear-gradient(180deg, rgba(13, 13, 13, 0.3) 0%, rgba(13, 13, 13, 0.9) 100%);
            --gradient-overlay-dark: linear-gradient(180deg, rgba(13, 13, 13, 0.5) 0%, rgba(13, 13, 13, 0.95) 100%);
            
            /* Status Colors */
            --color-success: #4ade80;
            --color-warning: #fbbf24;
            --color-error: #ef4444;
            --color-info: #60a5fa;
            --color-rating: #FFD700;
            
            /* Shadow Colors */
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.5);
            --shadow-xl: 0 10px 30px rgba(229, 9, 20, 0.3);
            --shadow-glow: 0 0 20px rgba(229, 9, 20, 0.4);
            --shadow-card: 0 4px 12px rgba(0, 0, 0, 0.3);
            
            /* Spacing */
            --spacing-xs: 0.25rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-2xl: 3rem;
            
            /* Border Radius */
            --radius-xs: 4px;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 25px;
            --radius-xl: 50px;
            --radius-full: 9999px;
            
            /* Transitions */
            --transition-fast: 0.15s ease;
            --transition-normal: 0.3s ease;
            --transition-slow: 0.5s ease;
            
            /* Z-Index */
            --z-dropdown: 1000;
            --z-sticky: 1010;
            --z-modal: 1020;
            --z-tooltip: 1030;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Light Mode Styles (Default) */
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #FFFFFF !important;
            color: #1F1F1F !important;
            min-height: 100vh;
            transition: background 0.3s ease, color 0.3s ease;
            font-weight: 400;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }
        
        p {
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            line-height: 1.7;
        }
        
        a {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }
        
        button {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        
        .bg-bg-primary {
            background-color: #FFFFFF !important;
        }
        
        .bg-bg-card {
            background-color: #FFFFFF !important;
            border-color: #E0E0E0 !important;
        }
        
        .bg-bg-card-hover {
            background-color: #F5F5F5 !important;
        }
        
        .text-text-primary {
            color: #1F1F1F !important;
        }
        
        .text-text-secondary {
            color: #666666 !important;
        }
        
        .text-text-tertiary {
            color: #808080 !important;
        }
        
        .text-text-muted {
            color: #999999 !important;
        }
        
        .border-border-primary {
            border-color: #E0E0E0 !important;
        }
        
        .border-border-secondary {
            border-color: #D0D0D0 !important;
        }
        
        nav {
            background-color: #FFFFFF !important;
            border-bottom-color: #E0E0E0 !important;
        }
        
        nav .text-text-primary {
            color: #1F1F1F !important;
        }
        
        footer {
            background-color: #F8F8F8 !important;
            border-top-color: #E0E0E0 !important;
        }
        
        footer .text-text-secondary {
            color: #666666 !important;
        }
        
        input, textarea, select {
            background-color: #F5F5F5 !important;
            border-color: #E0E0E0 !important;
            color: #1F1F1F !important;
        }
        
        input::placeholder, textarea::placeholder {
            color: #999999 !important;
        }
        
        /* Dark Mode Styles */
        body.dark-mode,
        html.dark body {
            background: linear-gradient(135deg, #0D0D0D 0%, #181818 100%) !important;
            color: #FFFFFF !important;
        }
        
        body.dark-mode .bg-bg-primary,
        html.dark .bg-bg-primary {
            background-color: #0D0D0D !important;
        }
        
        body.dark-mode .bg-bg-card,
        html.dark .bg-bg-card {
            background-color: #1F1F1F !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        body.dark-mode .bg-bg-card-hover,
        html.dark .bg-bg-card-hover {
            background-color: #2A2A2A !important;
        }
        
        body.dark-mode .text-text-primary,
        html.dark .text-text-primary {
            color: #FFFFFF !important;
        }
        
        body.dark-mode .text-text-secondary,
        html.dark .text-text-secondary {
            color: #B3B3B3 !important;
        }
        
        body.dark-mode .text-text-tertiary,
        html.dark .text-text-tertiary {
            color: #808080 !important;
        }
        
        body.dark-mode .text-text-muted,
        html.dark .text-text-muted {
            color: #666666 !important;
        }
        
        body.dark-mode .border-border-primary,
        html.dark .border-border-primary {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        body.dark-mode .border-border-secondary,
        html.dark .border-border-secondary {
            border-color: rgba(255, 255, 255, 0.05) !important;
        }
        
        body.dark-mode nav,
        html.dark nav {
            background-color: rgba(13, 13, 13, 0.95) !important;
            border-bottom-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        body.dark-mode nav .text-text-primary,
        html.dark nav .text-text-primary {
            color: #FFFFFF !important;
        }
        
        body.dark-mode footer,
        html.dark footer {
            background-color: #1F1F1F !important;
            border-top-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        body.dark-mode footer .text-text-secondary,
        html.dark footer .text-text-secondary {
            color: #B3B3B3 !important;
        }
        
        body.dark-mode input, 
        body.dark-mode textarea, 
        body.dark-mode select,
        html.dark input,
        html.dark textarea,
        html.dark select {
            background-color: #1F1F1F !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #FFFFFF !important;
        }
        
        body.dark-mode input::placeholder, 
        body.dark-mode textarea::placeholder,
        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #666666 !important;
        }
        
        /* Additional Dark Mode Rules for Cards and Text */
        body.dark-mode article,
        html.dark article {
            background-color: #1F1F1F !important;
        }
        
        body.dark-mode article h2,
        html.dark article h2 {
            color: #FFFFFF !important;
        }
        
        body.dark-mode article p,
        html.dark article p {
            color: #B3B3B3 !important;
        }
        
        body.dark-mode article .text-gray-900,
        html.dark article .text-gray-900 {
            color: #FFFFFF !important;
        }
        
        body.dark-mode article .text-gray-600,
        html.dark article .text-gray-600 {
            color: #B3B3B3 !important;
        }
        
        body.dark-mode article .text-gray-500,
        html.dark article .text-gray-500 {
            color: #808080 !important;
        }
        
        body.dark-mode .bg-white,
        html.dark .bg-white {
            background-color: #1F1F1F !important;
        }
        
        body.dark-mode .text-gray-900,
        html.dark .text-gray-900 {
            color: #FFFFFF !important;
        }
        
        body.dark-mode .text-gray-600,
        html.dark .text-gray-600 {
            color: #B3B3B3 !important;
        }
        
        body.dark-mode .text-gray-500,
        html.dark .text-gray-500 {
            color: #808080 !important;
        }
        
        body.dark-mode .border-gray-200,
        html.dark .border-gray-200 {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        body.dark-mode .bg-gray-50,
        html.dark .bg-gray-50 {
            background-color: #2A2A2A !important;
        }
        
        body.dark-mode .bg-gray-100,
        html.dark .bg-gray-100 {
            background-color: #2A2A2A !important;
        }
        
        body.dark-mode .hover\:bg-gray-50:hover,
        html.dark .hover\:bg-gray-50:hover {
            background-color: #2A2A2A !important;
        }
        
        /* Force dark mode styles for all elements */
        html.dark body article,
        body.dark-mode article {
            background-color: #1F1F1F !important;
        }
        
        /* Apply dark background to article divs, but exclude image containers and their children */
        html.dark body article > div:not(.aspect-video),
        body.dark-mode article > div:not(.aspect-video),
        html.dark body article div:not(.aspect-video):not(.aspect-video *) {
            background-color: #1F1F1F !important;
        }
        
        /* Explicitly exclude image containers from dark background */
        html.dark body article .aspect-video,
        body.dark-mode article .aspect-video,
        html.dark body article .aspect-video *,
        body.dark-mode article .aspect-video * {
            background-color: transparent !important;
        }
        
        /* Keep the container background visible only for placeholder */
        html.dark body article .aspect-video.bg-gray-200.dark\:bg-gray-800,
        body.dark-mode article .aspect-video.bg-gray-200.dark\:bg-gray-800 {
            background-color: rgba(31, 41, 55, 0.3) !important;
        }
        
        html.dark body article h2,
        body.dark-mode article h2 {
            color: #FFFFFF !important;
        }
        
        html.dark body article h2 span,
        body.dark-mode article h2 span {
            color: #B3B3B3 !important;
        }
        
        /* Ensure titles are always visible - Light Mode */
        article h2 {
            color: #1F1F1F !important;
        }
        
        article h2 span {
            color: #666666 !important;
        }
        
        /* Ensure titles are always visible - Dark Mode */
        html.dark article h2,
        body.dark-mode article h2 {
            color: #FFFFFF !important;
        }
        
        html.dark article h2 span,
        body.dark-mode article h2 span {
            color: #B3B3B3 !important;
        }
        
        /* Override any inline styles for titles */
        html.dark article h2[style*="color"],
        body.dark-mode article h2[style*="color"] {
            color: #FFFFFF !important;
        }
        
        html.dark article h2 span[style*="color"],
        body.dark-mode article h2 span[style*="color"] {
            color: #B3B3B3 !important;
        }
        
        html.dark body article p,
        body.dark-mode article p {
            color: #B3B3B3 !important;
        }
        
        html.dark body article .text-gray-500,
        body.dark-mode article .text-gray-500 {
            color: #808080 !important;
        }
        
        html.dark body .bg-white,
        body.dark-mode .bg-white {
            background-color: #1F1F1F !important;
        }
        
        html.dark body .text-gray-900,
        body.dark-mode .text-gray-900 {
            color: #FFFFFF !important;
        }
        
        html.dark body .text-gray-600,
        body.dark-mode .text-gray-600 {
            color: #B3B3B3 !important;
        }
        
        html.dark body .text-gray-500,
        body.dark-mode .text-gray-500 {
            color: #808080 !important;
        }
        
        html.dark body .border-gray-200,
        body.dark-mode .border-gray-200 {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        /* Sidebar dark mode fixes */
        html.dark body .lg\:col-span-1 div,
        body.dark-mode .lg\:col-span-1 div {
            background-color: #1F1F1F !important;
        }
        
        html.dark body .lg\:col-span-1 h3,
        body.dark-mode .lg\:col-span-1 h3 {
            color: #FFFFFF !important;
        }
        
        html.dark body .lg\:col-span-1 h4,
        body.dark-mode .lg\:col-span-1 h4 {
            color: #FFFFFF !important;
        }
        
        html.dark body .lg\:col-span-1 p,
        body.dark-mode .lg\:col-span-1 p {
            color: #B3B3B3 !important;
        }
        
        html.dark body .lg\:col-span-1 .border-b,
        body.dark-mode .lg\:col-span-1 .border-b {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        /* Pagination dark mode */
        html.dark body .mt-8 a,
        body.dark-mode .mt-8 a {
            background-color: #1F1F1F !important;
            color: #B3B3B3 !important;
        }
        
        html.dark body .mt-8 a:hover,
        body.dark-mode .mt-8 a:hover {
            background-color: #2A2A2A !important;
            color: #FFFFFF !important;
        }
        
        html.dark body .mt-8 .bg-accent,
        body.dark-mode .mt-8 .bg-accent {
            background-color: #E50914 !important;
            color: #FFFFFF !important;
        }
        
        html.dark body .mt-8 span,
        body.dark-mode .mt-8 span {
            color: #B3B3B3 !important;
        }
        
        /* Filter tabs styling - Light mode */
        .flex-wrap a.bg-white {
            background-color: #FFFFFF !important;
            color: #1F1F1F !important;
            border-color: #E0E0E0 !important;
        }
        
        .flex-wrap a.bg-white:hover {
            background-color: #F5F5F5 !important;
        }
        
        .flex-wrap a.bg-accent {
            background-color: #E50914 !important;
            color: #FFFFFF !important;
            border-color: #E50914 !important;
        }
        
        /* Filter tabs styling - Dark mode */
        html.dark .flex-wrap a.bg-white,
        body.dark-mode .flex-wrap a.bg-white {
            background-color: #1F1F1F !important;
            color: #B3B3B3 !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        html.dark .flex-wrap a.bg-white:hover,
        body.dark-mode .flex-wrap a.bg-white:hover {
            background-color: #2A2A2A !important;
            color: #FFFFFF !important;
        }
        
        html.dark .flex-wrap a.bg-accent,
        body.dark-mode .flex-wrap a.bg-accent {
            background-color: #E50914 !important;
            color: #FFFFFF !important;
            border-color: #E50914 !important;
        }
        
        /* Ensure image container divs don't hide images in dark mode */
        html.dark article .aspect-video,
        body.dark-mode article .aspect-video {
            background-color: transparent !important;
        }
        
        html.dark article .aspect-video.bg-gray-200,
        body.dark-mode article .aspect-video.bg-gray-200,
        html.dark article .aspect-video.dark\:bg-gray-800,
        body.dark-mode article .aspect-video.dark\:bg-gray-800 {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Ensure images are fully visible and bright in dark mode */
        html.dark article img,
        body.dark-mode article img,
        html.dark .aspect-video img,
        body.dark-mode .aspect-video img,
        html.dark article .aspect-video img,
        body.dark-mode article .aspect-video img,
        html.dark img[alt*="Movie"],
        body.dark-mode img[alt*="Movie"],
        html.dark img[alt*="TV Show"],
        body.dark-mode img[alt*="TV Show"] {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            filter: brightness(1.1) !important;
            -webkit-filter: brightness(1.1) !important;
            max-width: 100% !important;
            height: auto !important;
        }
        
        /* Ensure card images have proper stacking context and same size in dark mode */
        html.dark article .relative img,
        body.dark-mode article .relative img,
        html.dark article .aspect-video img,
        body.dark-mode article .aspect-video img {
            position: absolute !important;
            z-index: 1 !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
        }
        
        /* Ensure aspect-video container maintains exact same size in dark mode as light mode */
        html.dark article .aspect-video,
        body.dark-mode article .aspect-video {
            width: 100% !important;
            aspect-ratio: 16 / 9 !important;
            min-height: 0 !important;
            padding-bottom: 0 !important;
            padding-top: 0 !important;
            margin: 0 !important;
            border: none !important;
            box-shadow: none !important;
            max-width: 100% !important;
        }
        
        /* Ensure article containers maintain same dimensions in dark mode */
        html.dark article,
        body.dark-mode article {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }
        
        /* Ensure article links maintain same dimensions */
        html.dark article > a,
        body.dark-mode article > a {
            width: 100% !important;
            display: block !important;
        }
        
        /* Make overlay lighter in dark mode to show more of the image */
        html.dark article .absolute.inset-0.bg-gradient-to-t,
        body.dark-mode article .absolute.inset-0.bg-gradient-to-t {
            pointer-events: none !important;
            mix-blend-mode: normal !important;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.65) 0%, rgba(0, 0, 0, 0.15) 50%, transparent 70%) !important;
        }
        
        html.dark article .absolute.inset-0 > div,
        body.dark-mode article .absolute.inset-0 > div {
            pointer-events: auto !important;
        }
    </style>
</head>
<body>
    <nav class="sticky top-0 z-[100] bg-white backdrop-blur-lg shadow-lg border-b border-gray-200 dark:!bg-bg-primary/95 dark:!border-border-primary">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="text-xl sm:text-2xl md:text-3xl font-bold text-accent hover:text-accent-light transition-colors dark-mode:text-accent flex-shrink-0" style="font-family: 'Poppins', sans-serif; font-weight: 800; letter-spacing: -0.03em;">
                    Nazaarabox
                </a>
                
                <!-- Desktop Navigation - Always visible on md and above -->
                <ul class="hidden md:flex items-center gap-2 lg:gap-4 xl:gap-6 flex-wrap justify-center flex-1 mx-4">
                    <li><a href="{{ route('home') }}" class="text-gray-900 hover:text-accent transition-colors font-semibold dark:!text-white text-xs lg:text-sm xl:text-base whitespace-nowrap" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Home</a></li>
                    <li><a href="{{ route('movies.index') }}" class="text-gray-900 hover:text-accent transition-colors font-semibold dark:!text-white text-xs lg:text-sm xl:text-base whitespace-nowrap" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Movies</a></li>
                    <li><a href="{{ route('tv-shows.index') }}" class="text-gray-900 hover:text-accent transition-colors font-semibold dark:!text-white text-xs lg:text-sm xl:text-base whitespace-nowrap" style="font-family: 'Poppins', sans-serif; font-weight: 600;">TV Shows</a></li>
                    <li><a href="{{ route('cast.index') }}" class="text-gray-900 hover:text-accent transition-colors font-semibold dark:!text-white text-xs lg:text-sm xl:text-base whitespace-nowrap" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Cast</a></li>
                    <li><a href="{{ route('upcoming') }}" class="text-gray-900 hover:text-accent transition-colors font-semibold dark:!text-white text-xs lg:text-sm xl:text-base whitespace-nowrap" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Upcoming</a></li>
                    <li><a href="{{ route('completed') }}" class="text-gray-900 hover:text-accent transition-colors font-semibold dark:!text-white text-xs lg:text-sm xl:text-base whitespace-nowrap" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Completed</a></li>
                    <li><a href="{{ route('how-to-download') }}" class="text-gray-900 hover:text-accent transition-colors font-semibold dark:!text-white text-xs lg:text-sm xl:text-base whitespace-nowrap" style="font-family: 'Poppins', sans-serif; font-weight: 600;">How to Download</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-900 hover:text-accent transition-colors font-semibold dark:!text-white text-xs lg:text-sm xl:text-base whitespace-nowrap" style="font-family: 'Poppins', sans-serif; font-weight: 600;">About Us</a></li>
                    <li><a href="{{ route('dmca') }}" class="text-gray-900 hover:text-accent transition-colors font-semibold dark:!text-white text-xs lg:text-sm xl:text-base whitespace-nowrap" style="font-family: 'Poppins', sans-serif; font-weight: 600;">DMCA</a></li>
                </ul>
                
                <!-- Right Side Actions -->
                <div class="flex items-center gap-2 sm:gap-3 md:gap-4 flex-shrink-0">
                    <!-- Theme Toggle -->
                    <button id="themeToggle" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 border border-gray-300 transition-all dark:!bg-bg-card dark:!border-border-primary dark:!hover:bg-bg-card-hover" title="Toggle Theme">
                        <svg id="sunIcon" class="w-5 h-5 text-gray-900 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg id="moonIcon" class="w-5 h-5 text-white hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                    
                    <!-- Mobile Menu Toggle Button - Only visible on mobile -->
                    <button id="mobileMenuToggle" class="md:hidden p-2 rounded-lg text-gray-900 hover:text-accent dark:!text-white hover:bg-gray-100 dark:!hover:bg-bg-card-hover transition-all" aria-label="Toggle menu">
                        <svg id="menuIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg id="closeIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu Overlay -->
        <div id="mobileMenuOverlay" class="fixed inset-0 bg-black/50 z-[60] hidden md:hidden transition-opacity duration-300" onclick="closeMobileMenu()"></div>
        
        <!-- Mobile Menu - Full Screen on Mobile, Connected to Navbar -->
        <div id="mobileMenu" class="fixed top-16 md:top-0 md:right-0 left-0 md:left-auto h-[calc(100vh-4rem)] md:h-full w-full md:w-[85vw] md:max-w-md bg-white dark:!bg-bg-primary shadow-2xl z-[70] transform translate-x-full md:translate-x-full transition-transform duration-300 ease-in-out md:hidden overflow-y-auto">
            <div class="flex flex-col h-full">
                <!-- Mobile Menu Header -->
                <div class="flex items-center justify-between p-5 sm:p-6 border-b border-gray-200 dark:!border-border-primary bg-gradient-to-r from-accent/10 to-transparent">
                    <span class="text-2xl sm:text-3xl font-bold text-accent" style="font-family: 'Poppins', sans-serif; font-weight: 800;">Menu</span>
                    <button id="mobileMenuClose" onclick="closeMobileMenu()" class="p-3 rounded-lg text-gray-900 hover:bg-gray-100 dark:!text-white dark:!hover:bg-bg-card-hover transition-all" aria-label="Close menu">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Mobile Menu Items -->
                <nav class="flex-1 p-5 sm:p-6 overflow-y-auto">
                    <ul class="space-y-3 sm:space-y-4">
                        <li>
                            <a href="{{ route('home') }}" onclick="closeMobileMenu()" class="flex items-center px-5 sm:px-6 py-4 sm:py-5 rounded-xl text-gray-900 hover:bg-gray-100 hover:text-accent dark:!text-white dark:!hover:bg-bg-card-hover transition-all font-semibold text-lg sm:text-xl shadow-sm hover:shadow-md" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-4 sm:mr-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('movies.index') }}" onclick="closeMobileMenu()" class="flex items-center px-5 sm:px-6 py-4 sm:py-5 rounded-xl text-gray-900 hover:bg-gray-100 hover:text-accent dark:!text-white dark:!hover:bg-bg-card-hover transition-all font-semibold text-lg sm:text-xl shadow-sm hover:shadow-md" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-4 sm:mr-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
                                </svg>
                                <span>Movies</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tv-shows.index') }}" onclick="closeMobileMenu()" class="flex items-center px-5 sm:px-6 py-4 sm:py-5 rounded-xl text-gray-900 hover:bg-gray-100 hover:text-accent dark:!text-white dark:!hover:bg-bg-card-hover transition-all font-semibold text-lg sm:text-xl shadow-sm hover:shadow-md" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-4 sm:mr-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>TV Shows</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cast.index') }}" onclick="closeMobileMenu()" class="flex items-center px-5 sm:px-6 py-4 sm:py-5 rounded-xl text-gray-900 hover:bg-gray-100 hover:text-accent dark:!text-white dark:!hover:bg-bg-card-hover transition-all font-semibold text-lg sm:text-xl shadow-sm hover:shadow-md" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-4 sm:mr-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>Cast</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('upcoming') }}" onclick="closeMobileMenu()" class="flex items-center px-5 sm:px-6 py-4 sm:py-5 rounded-xl text-gray-900 hover:bg-gray-100 hover:text-accent dark:!text-white dark:!hover:bg-bg-card-hover transition-all font-semibold text-lg sm:text-xl shadow-sm hover:shadow-md" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-4 sm:mr-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Upcoming</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('completed') }}" onclick="closeMobileMenu()" class="flex items-center px-5 sm:px-6 py-4 sm:py-5 rounded-xl text-gray-900 hover:bg-gray-100 hover:text-accent dark:!text-white dark:!hover:bg-bg-card-hover transition-all font-semibold text-lg sm:text-xl shadow-sm hover:shadow-md" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-4 sm:mr-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Completed</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('how-to-download') }}" onclick="closeMobileMenu()" class="flex items-center px-5 sm:px-6 py-4 sm:py-5 rounded-xl text-gray-900 hover:bg-gray-100 hover:text-accent dark:!text-white dark:!hover:bg-bg-card-hover transition-all font-semibold text-lg sm:text-xl shadow-sm hover:shadow-md" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-4 sm:mr-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span>How to Download</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('about') }}" onclick="closeMobileMenu()" class="flex items-center px-5 sm:px-6 py-4 sm:py-5 rounded-xl text-gray-900 hover:bg-gray-100 hover:text-accent dark:!text-white dark:!hover:bg-bg-card-hover transition-all font-semibold text-lg sm:text-xl shadow-sm hover:shadow-md" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-4 sm:mr-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>About Us</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dmca') }}" onclick="closeMobileMenu()" class="flex items-center px-5 sm:px-6 py-4 sm:py-5 rounded-xl text-gray-900 hover:bg-gray-100 hover:text-accent dark:!text-white dark:!hover:bg-bg-card-hover transition-all font-semibold text-lg sm:text-xl shadow-sm hover:shadow-md" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-4 sm:mr-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <span>DMCA</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <!-- Mobile Menu Footer -->
                <div class="p-5 sm:p-6 border-t border-gray-200 dark:!border-border-primary bg-gray-50 dark:!bg-bg-card-hover">
                    <div class="text-center text-base sm:text-lg text-gray-600 dark:!text-text-secondary font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                        © {{ date('Y') }} Nazaarabox
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200 mt-12 dark:!bg-bg-card dark:!border-border-primary">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center space-y-4">
                <div class="flex justify-center items-center gap-4 mb-4">
                    <a href="#" class="text-gray-600 hover:text-accent transition-colors dark:!text-text-secondary">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-600 hover:text-accent transition-colors dark:!text-text-secondary">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221c.328 0 .593.266.593.593v2.716c0 .327-.265.593-.593.593h-1.306v1.306h1.306c.327 0 .593.265.593.593v2.716c0 .327-.266.593-.593.593h-2.716c-.328 0-.593-.266-.593-.593v-1.306H9.221v1.306c0 .327-.265.593-.593.593H5.912c-.327 0-.593-.266-.593-.593v-2.716c0-.328.266-.593.593-.593h1.306V9.221H5.912c-.327 0-.593-.265-.593-.593V5.912c0-.327.266-.593.593-.593h2.716c.328 0 .593.266.593.593v1.306h5.557V5.912c0-.327.265-.593.593-.593h2.716c.327 0 .593.266.593.593v2.716c0 .328-.266.593-.593.593h-1.306v1.306h1.306z"/>
                        </svg>
                    </a>
                </div>
                <p class="text-gray-600 text-sm dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    Copyright © {{ date('Y') }} - Nazaarabox
                </p>
            </div>
        </div>
    </footer>
    
    <script>
        // Theme Toggle Functionality
        const themeToggle = document.getElementById('themeToggle');
        const sunIcon = document.getElementById('sunIcon');
        const moonIcon = document.getElementById('moonIcon');
        const html = document.documentElement;
        const body = document.body;
        
        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        
        if (currentTheme === 'dark') {
            html.classList.add('dark');
            body.classList.add('dark-mode');
            sunIcon.classList.add('hidden');
            moonIcon.classList.remove('hidden');
        } else {
            // Light mode is default, show sun icon
            html.classList.remove('dark');
            body.classList.remove('dark-mode');
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
        }
        
        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            body.classList.toggle('dark-mode');
            
            if (html.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            } else {
                localStorage.setItem('theme', 'light');
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            }
        });
        
        // Mobile Menu Functionality
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
        const menuIcon = document.getElementById('menuIcon');
        const closeIcon = document.getElementById('closeIcon');
        const bodyElement = document.body;
        
        function openMobileMenu() {
            mobileMenu.classList.remove('translate-x-full');
            mobileMenuOverlay.classList.remove('hidden');
            bodyElement.style.overflow = 'hidden'; // Prevent body scroll when menu is open
            menuIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
        }
        
        function closeMobileMenu() {
            mobileMenu.classList.add('translate-x-full');
            mobileMenuOverlay.classList.add('hidden');
            bodyElement.style.overflow = ''; // Restore body scroll
            menuIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }
        
        // Toggle mobile menu
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', () => {
                if (mobileMenu.classList.contains('translate-x-full')) {
                    openMobileMenu();
                } else {
                    closeMobileMenu();
                }
            });
        }
        
        // Close menu when clicking overlay
        if (mobileMenuOverlay) {
            mobileMenuOverlay.addEventListener('click', closeMobileMenu);
        }
        
        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('translate-x-full')) {
                closeMobileMenu();
            }
        });
        
        // Close menu when window is resized to desktop size
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768 && !mobileMenu.classList.contains('translate-x-full')) {
                closeMobileMenu();
            }
        });
        
        // Make functions globally available for onclick handlers
        window.openMobileMenu = openMobileMenu;
        window.closeMobileMenu = closeMobileMenu;
    </script>
    
    <!-- OneSignal Push Notifications -->
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script src="{{ asset('js/onesignal.js') }}" defer></script>
    <style>
        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slide-down {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-up {
            animation: slide-up 0.3s ease-out;
        }
        .animate-slide-down {
            animation: slide-down 0.3s ease-out;
        }
    </style>
    <script>
      // OneSignal will be initialized by the OneSignalManager class
      // The manager handles all permission and subscription logic automatically
      window.OneSignalDeferred = window.OneSignalDeferred || [];
      
    </script>
    
    @stack('scripts')
</body>
</html>

