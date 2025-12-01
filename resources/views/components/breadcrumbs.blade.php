@props(['items'])

@php
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
@endphp

@if(count($breadcrumbs) > 1)
<nav aria-label="Breadcrumb" class="mb-6">
    <ol class="flex flex-wrap items-center gap-2 text-sm" itemscope itemtype="https://schema.org/BreadcrumbList" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
        @foreach($breadcrumbs as $index => $crumb)
        <li class="flex items-center" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            @if($crumb['url'] && !$loop->last)
                <a href="{{ $crumb['url'] }}" 
                   class="text-gray-600 dark:!text-text-secondary hover:text-accent transition-colors" 
                   itemprop="item">
                    <span itemprop="name">{{ $crumb['label'] }}</span>
                </a>
                <meta itemprop="position" content="{{ $crumb['position'] }}">
            @else
                <span class="text-gray-900 dark:!text-white font-semibold" itemprop="name">{{ $crumb['label'] }}</span>
                <meta itemprop="position" content="{{ $crumb['position'] }}">
            @endif
            
            @if(!$loop->last)
                <svg class="w-4 h-4 mx-2 text-gray-400 dark:!text-text-tertiary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            @endif
        </li>
        @endforeach
    </ol>
</nav>
@endif

