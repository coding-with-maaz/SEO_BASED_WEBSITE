@extends('layouts.app')

@section('title', 'View Content - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-accent dark:!text-text-secondary dark:!hover:text-accent transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    ← Dashboard
                </a>
                <span class="text-gray-400">|</span>
                <a href="{{ route('admin.contents.index') }}" class="text-gray-600 hover:text-accent dark:!text-text-secondary dark:!hover:text-accent transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    All Content
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                {{ $content->title }}
            </h1>
            <p class="text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Content Details
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.contents.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Back to List
            </a>
            <a href="{{ route('admin.contents.edit', $content) }}" class="px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Edit
            </a>
            @if(in_array($content->type, ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']))
                <a href="{{ route('admin.episodes.index', $content) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Manage Episodes
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Poster and Basic Info -->
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <div class="flex gap-6">
                    @if($content->poster_path)
                        @php
                            $posterUrl = str_starts_with($content->poster_path, 'http') 
                                ? $content->poster_path 
                                : (in_array($content->content_type ?? 'custom', ['tmdb', 'article']) || str_starts_with($content->poster_path, '/')
                                    ? app(\App\Services\TmdbService::class)->getImageUrl($content->poster_path, 'w500')
                                    : asset('storage/' . $content->poster_path));
                        @endphp
                        <img src="{{ $posterUrl }}" alt="{{ $content->title }}" class="w-48 h-auto rounded-lg">
                    @endif
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                            {{ $content->title }}
                        </h2>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-3 py-1 bg-accent text-white rounded-full text-sm font-semibold" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                {{ ucfirst(str_replace('_', ' ', $content->type)) }}
                            </span>
                            @if(in_array($content->content_type ?? 'custom', ['tmdb', 'article']))
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold dark:!bg-blue-900/20 dark:!text-blue-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                    {{ $content->content_type === 'article' ? 'Article' : 'TMDB' }}
                                </span>
                            @endif
                            @if($content->status === 'published')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold dark:!bg-green-900/20 dark:!text-green-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                    Published
                                </span>
                            @endif
                            @if($content->is_featured)
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold dark:!bg-yellow-900/20 dark:!text-yellow-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                    Featured
                                </span>
                            @endif
                        </div>
                        @if($content->rating)
                            <p class="text-gray-600 dark:!text-text-secondary mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                <span class="font-semibold">Rating:</span> {{ $content->rating }}/10
                            </p>
                        @endif
                        @if($content->release_date)
                            <p class="text-gray-600 dark:!text-text-secondary mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                <span class="font-semibold">Release Date:</span> {{ $content->release_date->format('F d, Y') }}
                            </p>
                        @endif
                        @if($content->dubbing_language)
                            <p class="text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                <span class="font-semibold">Dubbing:</span> {{ ucfirst($content->dubbing_language) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($content->description)
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-3" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Description
                </h3>
                <p class="text-gray-700 dark:!text-text-secondary leading-relaxed" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    {{ $content->description }}
                </p>
            </div>
            @endif

            <!-- Servers -->
            @php
                $servers = is_array($content->servers) ? $content->servers : [];
            @endphp
            @if(count($servers) > 0)
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Servers ({{ count($servers) }})
                </h3>
                <div class="space-y-3">
                    @foreach($servers as $server)
                        <div class="border border-gray-200 dark:!border-border-secondary rounded-lg p-4 bg-gray-50 dark:!bg-bg-card-hover">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:!text-white mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                        {{ $server['server_name'] ?? 'Server' }}
                                        @if(isset($server['quality']))
                                            <span class="text-sm font-normal text-gray-600 dark:!text-text-secondary">({{ $server['quality'] }})</span>
                                        @endif
                                    </h4>
                                    @if(isset($server['watch_link']))
                                        <a href="{{ $server['watch_link'] }}" target="_blank" class="text-sm text-accent hover:underline">
                                            Watch Link
                                        </a>
                                    @endif
                                    @if(isset($server['download_link']))
                                        <span class="text-gray-400 mx-2">•</span>
                                        <a href="{{ $server['download_link'] }}" target="_blank" class="text-sm text-accent hover:underline">
                                            Download Link
                                        </a>
                                    @endif
                                </div>
                                @if(isset($server['is_active']) && !$server['is_active'])
                                    <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded dark:!bg-red-900/20 dark:!text-red-400">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Links -->
            @if($content->watch_link || $content->download_link)
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Direct Links
                </h3>
                <div class="space-y-2">
                    @if($content->watch_link)
                        <p class="text-gray-700 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            <span class="font-semibold">Watch:</span> 
                            <a href="{{ $content->watch_link }}" target="_blank" class="text-accent hover:underline break-all">
                                {{ $content->watch_link }}
                            </a>
                        </p>
                    @endif
                    @if($content->download_link)
                        <p class="text-gray-700 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            <span class="font-semibold">Download:</span> 
                            <a href="{{ $content->download_link }}" target="_blank" class="text-accent hover:underline break-all">
                                {{ $content->download_link }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Info -->
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Quick Info
                </h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Status:</span>
                        <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            {{ ucfirst($content->status) }}
                        </span>
                    </div>
                    @if($content->tmdb_id)
                        <div>
                            <span class="font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">TMDB ID:</span>
                            <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                {{ $content->tmdb_id }}
                            </span>
                        </div>
                    @endif
                    @if($content->duration)
                        <div>
                            <span class="font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Duration:</span>
                            <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                {{ $content->duration }} min
                            </span>
                        </div>
                    @endif
                    @if(in_array($content->type, ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']))
                        @if($content->series_status)
                            <div>
                                <span class="font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Series Status:</span>
                                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                    {{ ucfirst(str_replace('_', ' ', $content->series_status)) }}
                                </span>
                            </div>
                        @endif
                        @if($content->episode_count)
                            <div>
                                <span class="font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Episodes:</span>
                                <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                    {{ $content->episode_count }}
                                </span>
                            </div>
                        @endif
                    @endif
                    <div>
                        <span class="font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Views:</span>
                        <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            {{ number_format($content->views ?? 0) }}
                        </span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Created:</span>
                        <span class="text-gray-600 dark:!text-text-secondary ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            {{ $content->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Actions
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.contents.edit', $content) }}" class="block w-full px-4 py-2 bg-accent hover:bg-accent-light text-white text-center rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Edit Content
                    </a>
                    @if(in_array($content->type, ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']))
                        <a href="{{ route('admin.episodes.index', $content) }}" class="block w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-center rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Manage Episodes
                        </a>
                    @endif
                    @php
                        $publicRoute = $content->type === 'movie' 
                            ? route('movies.show', $content->slug ?? $content->id)
                            : route('tv-shows.show', $content->slug ?? $content->id);
                    @endphp
                    <a href="{{ $publicRoute }}" target="_blank" class="block w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        View Public Page
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

