@extends('layouts.app')

@section('title', 'Edit Content - Admin')

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
                Edit Content
            </h1>
            <p class="text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                {{ $content->title }}
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.contents.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Back to List
            </a>
            @if(in_array($content->type, ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']))
                <a href="{{ route('admin.episodes.index', $content) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Manage Episodes
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg dark:!bg-green-900/20 dark:!border-green-700 dark:!text-green-400">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg dark:!bg-red-900/20 dark:!border-red-700 dark:!text-red-400">
        {{ session('error') }}
    </div>
    @endif

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:!border-border-secondary">
        <nav class="flex gap-4">
            <button onclick="switchTab('details')" id="tab-details" class="tab-button px-6 py-3 font-semibold text-accent border-b-2 border-accent" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Content Details
            </button>
            <button onclick="switchTab('servers')" id="tab-servers" class="tab-button px-6 py-3 font-semibold text-gray-600 border-b-2 border-transparent hover:text-accent dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Servers Management
            </button>
        </nav>
    </div>

    <!-- Details Tab -->
    <div id="panel-details" class="tab-panel">
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <form action="{{ route('admin.contents.update', $content) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title', $content->title) }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Content Type</label>
                            <select name="type" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                                <option value="">Select Type</option>
                                @foreach($contentTypes as $type => $label)
                                    <option value="{{ $type }}" {{ old('type', $content->type) === $type ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Description</label>
                            <textarea name="description" rows="5"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">{{ old('description', $content->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Poster URL</label>
                            <input type="text" name="poster_path" value="{{ old('poster_path', $content->poster_path) }}" placeholder="https://..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @if($content->poster_path)
                                @php
                                    $posterUrl = str_starts_with($content->poster_path, 'http') 
                                        ? $content->poster_path 
                                        : ($content->content_type === 'tmdb' 
                                            ? app(\App\Services\TmdbService::class)->getImageUrl($content->poster_path, 'w342')
                                            : asset('storage/' . $content->poster_path));
                                @endphp
                                <img src="{{ $posterUrl }}" alt="Poster" class="mt-2 w-32 h-auto rounded">
                            @endif
                            @error('poster_path')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Backdrop URL</label>
                            <input type="text" name="backdrop_path" value="{{ old('backdrop_path', $content->backdrop_path) }}" placeholder="https://..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @if($content->backdrop_path)
                                @php
                                    $backdropUrl = str_starts_with($content->backdrop_path, 'http') 
                                        ? $content->backdrop_path 
                                        : ($content->content_type === 'tmdb' 
                                            ? app(\App\Services\TmdbService::class)->getImageUrl($content->backdrop_path, 'w780')
                                            : $content->backdrop_path);
                                @endphp
                                <img src="{{ $backdropUrl }}" alt="Backdrop" class="mt-2 w-full max-w-md h-auto rounded">
                            @endif
                            @error('backdrop_path')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Release Date</label>
                            <input type="date" name="release_date" value="{{ old('release_date', $content->release_date?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @error('release_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Rating (0-10)</label>
                            <input type="number" name="rating" value="{{ old('rating', $content->rating) }}" min="0" max="10" step="0.1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @error('rating')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Status</label>
                            <select name="status" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                                <option value="published" {{ old('status', $content->status) === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ old('status', $content->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="upcoming" {{ old('status', $content->status) === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @if(in_array($content->type, ['tv_show', 'web_series', 'anime', 'reality_show', 'talk_show']))
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Series Status</label>
                            <select name="series_status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                                <option value="">Select Status</option>
                                @foreach(\App\Models\Content::getSeriesStatuses() as $status => $label)
                                    <option value="{{ $status }}" {{ old('series_status', $content->series_status) === $status ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('series_status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Dubbing Language</label>
                            <select name="dubbing_language"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                                <option value="">Select Language</option>
                                @foreach($dubbingLanguages as $lang => $label)
                                    <option value="{{ $lang }}" {{ old('dubbing_language', $content->dubbing_language) === $lang ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('dubbing_language')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Watch Link</label>
                            <input type="url" name="watch_link" value="{{ old('watch_link', $content->watch_link) }}" placeholder="https://..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @error('watch_link')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Download Link</label>
                            <input type="url" name="download_link" value="{{ old('download_link', $content->download_link) }}" placeholder="https://..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @error('download_link')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $content->is_featured) ? 'checked' : '' }}
                                       class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent">
                                <span class="text-sm font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Featured Content</span>
                            </label>
                        </div>

                        <input type="hidden" name="content_type" value="{{ $content->content_type }}">
                        @if($content->tmdb_id)
                            <input type="hidden" name="tmdb_id" value="{{ $content->tmdb_id }}">
                        @endif
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Update Content
                    </button>
                    <a href="{{ route('admin.contents.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Servers Management Tab -->
    <div id="panel-servers" class="tab-panel hidden">
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Servers Management
                </h2>
                <button onclick="showAddServerModal()" class="px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Add Server
                </button>
            </div>

            @php
                // Get normalized servers
                $servers = $content->getNormalizedServers();
            @endphp

            @if(count($servers) > 0)
                <div class="space-y-4">
                    @foreach($servers as $index => $server)
                        <div class="border border-gray-200 dark:!border-border-secondary rounded-lg p-4 bg-gray-50 dark:!bg-bg-card-hover">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                            {{ $server['name'] ?? 'Server ' . ($index + 1) }}
                                        </h3>
                                        @if(!empty($server['quality']))
                                            <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded dark:!bg-blue-900/20 dark:!text-blue-400">
                                                {{ $server['quality'] }}
                                            </span>
                                        @endif
                                        @if(isset($server['active']) && !$server['active'])
                                            <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded dark:!bg-red-900/20 dark:!text-red-400">
                                                Inactive
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded dark:!bg-green-900/20 dark:!text-green-400">
                                                Active
                                            </span>
                                        @endif
                                    </div>
                                    @if(!empty($server['url']))
                                        <p class="text-sm text-gray-600 dark:!text-text-secondary mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                            <span class="font-semibold">Watch URL:</span> 
                                            <a href="{{ $server['url'] }}" target="_blank" class="text-accent hover:underline break-all">
                                                {{ Str::limit($server['url'], 60) }}
                                            </a>
                                        </p>
                                    @endif
                                    @if(isset($server['download_link']))
                                        <p class="text-sm text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                            <span class="font-semibold">Download:</span> 
                                            <a href="{{ $server['download_link'] }}" target="_blank" class="text-accent hover:underline break-all">
                                                {{ Str::limit($server['download_link'], 60) }}
                                            </a>
                                        </p>
                                    @endif
                                </div>
                                <div class="flex gap-2 ml-4">
                                    <button onclick="showEditServerModal({{ json_encode($server) }}, {{ $index }})" 
                                            class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.contents.servers.destroy', $content) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="server_id" value="{{ $server['id'] ?? '' }}">
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this server?')"
                                                class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-gray-50 dark:!bg-bg-card-hover rounded-lg border border-gray-200 dark:!border-border-secondary">
                    <p class="text-gray-600 dark:!text-text-secondary mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        No servers added yet. Click "Add Server" to add one.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Server Modal -->
<div id="add-server-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:!bg-bg-card rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Add New Server
        </h3>
        <form action="{{ route('admin.contents.servers.store', $content) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Server Name *</label>
                    <input type="text" name="server_name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Quality</label>
                    <input type="text" name="quality" placeholder="HD, 720p, 1080p, etc."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Watch Link</label>
                    <input type="url" name="watch_link" placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Download Link</label>
                    <input type="url" name="download_link" placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Sort Order</label>
                    <input type="number" name="sort_order" value="0" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Add Server
                </button>
                <button type="button" onclick="hideAddServerModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card-hover dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Server Modal -->
<div id="edit-server-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:!bg-bg-card rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Edit Server
        </h3>
        <form id="edit-server-form" action="{{ route('admin.contents.servers.update', $content) }}" method="POST">
            @csrf
            <input type="hidden" id="edit-server-id" name="server_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Server Name *</label>
                    <input type="text" id="edit-server-name" name="server_name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Quality</label>
                    <input type="text" id="edit-server-quality" name="quality" placeholder="HD, 720p, 1080p, etc."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Watch Link</label>
                    <input type="url" id="edit-server-watch" name="watch_link" placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Download Link</label>
                    <input type="url" id="edit-server-download" name="download_link" placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Sort Order</label>
                    <input type="number" id="edit-server-order" name="sort_order" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="edit-server-active" name="is_active" value="1"
                               class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent">
                        <span class="text-sm font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Active</span>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Update Server
                </button>
                <button type="button" onclick="hideEditServerModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card-hover dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Tab switching
function switchTab(tab) {
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('text-accent', 'border-accent');
        button.classList.add('text-gray-600', 'border-transparent', 'dark:!text-text-secondary');
    });
    
    document.getElementById('panel-' + tab).classList.remove('hidden');
    
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('text-gray-600', 'border-transparent', 'dark:!text-text-secondary');
    activeTab.classList.add('text-accent', 'border-accent');
}

// Server modals
function showAddServerModal() {
    document.getElementById('add-server-modal').classList.remove('hidden');
}

function hideAddServerModal() {
    document.getElementById('add-server-modal').classList.add('hidden');
    document.getElementById('add-server-modal').querySelector('form').reset();
}

function showEditServerModal(server, index) {
    document.getElementById('edit-server-id').value = server.id || '';
    document.getElementById('edit-server-name').value = server.server_name || '';
    document.getElementById('edit-server-quality').value = server.quality || '';
    document.getElementById('edit-server-watch').value = server.watch_link || '';
    document.getElementById('edit-server-download').value = server.download_link || '';
    document.getElementById('edit-server-order').value = server.sort_order || index;
    document.getElementById('edit-server-active').checked = server.is_active !== false;
    
    document.getElementById('edit-server-modal').classList.remove('hidden');
}

function hideEditServerModal() {
    document.getElementById('edit-server-modal').classList.add('hidden');
}

// Close modals on outside click
document.getElementById('add-server-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideAddServerModal();
    }
});

document.getElementById('edit-server-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideEditServerModal();
    }
});
</script>
@endsection

