@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Admin Dashboard
        </h1>
        <p class="text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
            Welcome to the admin panel. Here's an overview of your content management system.
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Content -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:!text-text-secondary uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Total Content
                    </p>
                    <p class="text-3xl font-bold text-gray-900 dark:!text-white mt-2" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                        {{ number_format($totalContent) }}
                    </p>
                </div>
                <div class="p-3 bg-blue-100 dark:!bg-blue-900/20 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600 dark:!text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 dark:!text-green-400 font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    {{ number_format($publishedContent) }} Published
                </span>
                <span class="text-gray-400 mx-2">•</span>
                <span class="text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    {{ number_format($draftContent) }} Draft
                </span>
            </div>
        </div>

        <!-- Movies -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:!text-text-secondary uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Movies
                    </p>
                    <p class="text-3xl font-bold text-gray-900 dark:!text-white mt-2" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                        {{ number_format($totalMovies) }}
                    </p>
                </div>
                <div class="p-3 bg-purple-100 dark:!bg-purple-900/20 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600 dark:!text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Movies, Documentaries, Short Films
            </div>
        </div>

        <!-- TV Shows -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:!text-text-secondary uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        TV Shows
                    </p>
                    <p class="text-3xl font-bold text-gray-900 dark:!text-white mt-2" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                        {{ number_format($totalTvShows) }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 dark:!bg-green-900/20 rounded-lg">
                    <svg class="w-8 h-8 text-green-600 dark:!text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-600 dark:!text-text-secondary font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    {{ number_format($totalEpisodes) }} Episodes
                </span>
            </div>
        </div>

        <!-- Total Views -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 dark:!text-text-secondary uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Total Views
                    </p>
                    <p class="text-3xl font-bold text-gray-900 dark:!text-white mt-2" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                        {{ number_format($totalViews) }}
                    </p>
                </div>
                <div class="p-3 bg-orange-100 dark:!bg-orange-900/20 rounded-lg">
                    <svg class="w-8 h-8 text-orange-600 dark:!text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                All time views
            </div>
        </div>
    </div>

    <!-- Secondary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Published Content -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-4">
            <p class="text-sm text-gray-600 dark:!text-text-secondary mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Published</p>
            <p class="text-2xl font-bold text-green-600 dark:!text-green-400" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($publishedContent) }}</p>
        </div>

        <!-- Draft Content -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-4">
            <p class="text-sm text-gray-600 dark:!text-text-secondary mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Draft</p>
            <p class="text-2xl font-bold text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($draftContent) }}</p>
        </div>

        <!-- Featured Content -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-4">
            <p class="text-sm text-gray-600 dark:!text-text-secondary mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Featured</p>
            <p class="text-2xl font-bold text-yellow-600 dark:!text-yellow-400" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($featuredContent) }}</p>
        </div>

        <!-- This Month -->
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-4">
            <p class="text-sm text-gray-600 dark:!text-text-secondary mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600;">This Month</p>
            <p class="text-2xl font-bold text-blue-600 dark:!text-blue-400" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($thisMonthContent) }}</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Recent Content -->
        <div class="lg:col-span-2 bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Recent Content
                </h2>
                <a href="{{ route('admin.contents.index') }}" class="text-sm text-accent hover:underline font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    View All →
                </a>
            </div>
            
            @if($recentContent->count() > 0)
                <div class="space-y-4">
                    @foreach($recentContent as $content)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 dark:!bg-bg-card-hover rounded-lg hover:bg-gray-100 dark:!hover:bg-bg-card-hover transition-colors">
                            <div class="w-16 h-24 rounded overflow-hidden bg-gray-200 dark:!bg-gray-700 flex-shrink-0">
                                @if($content->poster_path)
                                    @php
                                        $posterUrl = str_starts_with($content->poster_path, 'http') 
                                            ? $content->poster_path 
                                            : (in_array($content->content_type ?? 'custom', ['tmdb', 'article']) || str_starts_with($content->poster_path, '/')
                                                ? app(\App\Services\TmdbService::class)->getImageUrl($content->poster_path, 'w185')
                                                : asset('storage/' . $content->poster_path));
                                    @endphp
                                    <img src="{{ $posterUrl }}" alt="{{ $content->title }}" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/185x278?text=No+Image'">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Image</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 dark:!text-white mb-1 truncate" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                    {{ $content->title }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:!text-text-secondary mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                    {{ ucfirst(str_replace('_', ' ', $content->type)) }}
                                    @if(in_array($content->content_type ?? 'custom', ['tmdb', 'article']))
                                        <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs dark:!bg-blue-900/20 dark:!text-blue-400">{{ $content->content_type === 'article' ? 'Article' : 'TMDB' }}</span>
                                    @endif
                                </p>
                                <div class="flex items-center gap-3 text-xs text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                    <span>{{ $content->created_at->diffForHumans() }}</span>
                                    @if($content->status === 'published')
                                        <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded dark:!bg-green-900/20 dark:!text-green-400">Published</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-800 rounded dark:!bg-gray-800 dark:!text-gray-400">Draft</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('admin.contents.edit', $content) }}" class="px-3 py-1 bg-accent hover:bg-accent-light text-white rounded text-sm transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                    Edit
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        No content yet. <a href="{{ route('admin.contents.create') }}" class="text-accent hover:underline">Create your first content</a>
                    </p>
                </div>
            @endif
        </div>

        <!-- Quick Actions & Stats -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Quick Actions
                </h2>
                <div class="space-y-3">
                    <a href="{{ route('admin.contents.create') }}" class="block w-full px-4 py-3 bg-accent hover:bg-accent-light text-white text-center rounded-lg transition-colors font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Add New Content
                    </a>
                    <a href="{{ route('admin.contents.index') }}" class="block w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-900 text-center rounded-lg transition-colors dark:!bg-bg-card-hover dark:!text-white dark:!hover:bg-bg-card-hover font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Manage Content
                    </a>
                    <a href="{{ route('admin.servers.index') }}" class="block w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-lg transition-colors font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Movie Server Management
                    </a>
                    <a href="{{ route('admin.page-seo.index') }}" class="block w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white text-center rounded-lg transition-colors font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Public Pages SEO Management
                    </a>
                    <a href="{{ route('admin.comments.index') }}" class="block w-full px-4 py-3 bg-orange-600 hover:bg-orange-700 text-white text-center rounded-lg transition-colors font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Comments Management
                    </a>
                </div>
            </div>

            <!-- Content Source -->
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Content Source
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">TMDB</span>
                        <span class="font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($tmdbContent) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Custom</span>
                        <span class="font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($customContent) }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Recent Activity
                </h2>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-accent rounded-full mt-2"></div>
                        <div>
                            <p class="text-gray-900 dark:!text-white font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                {{ $thisWeekContent }} content added this week
                            </p>
                            <p class="text-gray-600 dark:!text-text-secondary text-xs" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                {{ $thisMonthContent }} this month
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-gray-900 dark:!text-white font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                {{ $thisMonthEpisodes }} episodes added this month
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Viewed Content -->
    @if($topViewedContent->count() > 0)
    <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-6" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Most Viewed Content
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach($topViewedContent as $content)
                <div class="group cursor-pointer">
                    <div class="relative aspect-[2/3] rounded-lg overflow-hidden bg-gray-200 dark:!bg-gray-700 mb-2">
                        @if($content->poster_path)
                            @php
                                $posterUrl = str_starts_with($content->poster_path, 'http') 
                                    ? $content->poster_path 
                                    : (in_array($content->content_type ?? 'custom', ['tmdb', 'article']) || str_starts_with($content->poster_path, '/')
                                        ? app(\App\Services\TmdbService::class)->getImageUrl($content->poster_path, 'w342')
                                        : asset('storage/' . $content->poster_path));
                            @endphp
                            <img src="{{ $posterUrl }}" alt="{{ $content->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.src='https://via.placeholder.com/342x513?text=No+Image'">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Image</div>
                        @endif
                        <div class="absolute top-2 right-2 px-2 py-1 bg-black/70 text-white text-xs font-semibold rounded">
                            {{ number_format($content->views ?? 0) }} views
                        </div>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:!text-white text-sm truncate group-hover:text-accent transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        {{ $content->title }}
                    </h3>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

