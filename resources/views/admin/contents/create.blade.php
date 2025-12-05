@extends('layouts.app')

@section('title', 'Create Content - Admin')

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
                Create New Content
            </h1>
            <p class="text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Import from TMDB or create custom content
            </p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:!border-border-secondary">
        <nav class="flex gap-4">
            <button onclick="switchTab('tmdb')" id="tab-tmdb" class="tab-button px-6 py-3 font-semibold text-accent border-b-2 border-accent" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Import from TMDB
            </button>
            <button onclick="switchTab('custom')" id="tab-custom" class="tab-button px-6 py-3 font-semibold text-gray-600 border-b-2 border-transparent hover:text-accent dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Create Custom Content
            </button>
            <button onclick="switchTab('article')" id="tab-article" class="tab-button px-6 py-3 font-semibold text-gray-600 border-b-2 border-transparent hover:text-accent dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Create Article Content
            </button>
        </nav>
    </div>

    <!-- TMDB Import Tab -->
    <div id="panel-tmdb" class="tab-panel">
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Search and Import from TMDB
            </h2>

            <form id="tmdb-search-form" class="mb-6">
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Content Type</label>
                        <select id="tmdb-type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            <option value="movie">Movie</option>
                            <option value="tv">TV Show</option>
                        </select>
                    </div>
                    <div class="flex-[2]">
                        <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Search TMDB</label>
                        <div class="flex gap-2">
                            <input type="text" id="tmdb-query" placeholder="Enter movie or TV show name..." 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            <button type="submit" class="px-6 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Search
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    <p class="text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Add Server Embeds (automatically add embed links using TMDB ID):
                    </p>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="tmdb-add-vidsrc" name="embed_servers[]" value="vidsrc" 
                               class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent dark:!bg-bg-card-hover dark:!border-border-primary">
                        <span class="text-sm font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Vidsrc.icu
                        </span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="tmdb-add-vidlink" name="embed_servers[]" value="vidlink" 
                               class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent dark:!bg-bg-card-hover dark:!border-border-primary">
                        <span class="text-sm font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Vidlink.pro
                        </span>
                    </label>
                    <p class="text-xs text-gray-500 dark:!text-text-secondary mt-1 ml-6" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        For movies: Adds watch link. For TV shows: Adds embed servers for all episodes when created. You can select both services.
                    </p>
                </div>
            </form>

            <!-- Loading indicator -->
            <div id="tmdb-loading" class="hidden text-center py-8">
                <p class="text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Searching TMDB...</p>
            </div>

            <!-- Search Results -->
            <div id="tmdb-results" class="hidden">
                <h3 class="text-lg font-semibold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Search Results</h3>
                <div id="tmdb-results-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
            </div>
        </div>
    </div>

    <!-- Article Content Tab -->
    <div id="panel-article" class="tab-panel hidden">
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Create Article Content with TMDB
            </h2>
            <p class="text-gray-600 dark:!text-text-secondary mb-6" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Search and select a movie or TV show from TMDB to create an article with backdrop image and watch/download buttons.
            </p>

            <form id="article-search-form" class="mb-6">
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Content Type</label>
                        <select id="article-tmdb-type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            <option value="movie">Movie</option>
                            <option value="tv">TV Show</option>
                        </select>
                    </div>
                    <div class="flex-[2]">
                        <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Search TMDB</label>
                        <div class="flex gap-2">
                            <input type="text" id="article-tmdb-query" placeholder="Enter movie or TV show name..." 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            <button type="submit" class="px-6 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Loading indicator -->
            <div id="article-loading" class="hidden text-center py-8">
                <p class="text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Searching TMDB...</p>
            </div>

            <!-- Search Results -->
            <div id="article-results" class="hidden mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Select Content</h3>
                <div id="article-results-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
            </div>

            <!-- Article Form (shown after selection) -->
            <div id="article-form-container" class="hidden">
                <form action="{{ route('admin.contents.article.store') }}" method="POST" id="article-content-form">
                    @csrf
                    <input type="hidden" name="tmdb_id" id="article-tmdb-id">
                    <input type="hidden" name="tmdb_type" id="article-tmdb-type-value">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Selected Content Preview -->
                        <div class="lg:col-span-2">
                            <div id="article-selected-preview" class="bg-gray-50 dark:!bg-bg-card-hover rounded-lg border border-gray-200 dark:!border-border-secondary p-4 mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Selected Content</h3>
                                <div id="article-preview-content" class="flex gap-4">
                                    <!-- Preview will be inserted here -->
                                </div>
                            </div>
                        </div>

                        <!-- Article Content -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Article Content (HTML allowed)
                            </label>
                            <textarea name="article_content" id="article-content-text" rows="10"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white"
                                      placeholder="Write your article content here... HTML is allowed."></textarea>
                            <p class="text-sm text-gray-500 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                This content will be displayed in the article. You can use HTML for formatting.
                            </p>
                        </div>

                        <!-- Watch Link -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Watch Link
                            </label>
                            <input type="url" name="watch_link" id="article-watch-link" placeholder="https://..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            <p class="text-sm text-gray-500 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                URL for the watch button
                            </p>
                        </div>

                        <!-- Download Link -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Download Link
                            </label>
                            <input type="url" name="download_link" id="article-download-link" placeholder="https://..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            <p class="text-sm text-gray-500 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                URL for the download button
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                                <option value="published" selected>Published</option>
                                <option value="draft">Draft</option>
                                <option value="upcoming">Upcoming</option>
                            </select>
                        </div>

                        <!-- Add Embed Option -->
                        <div class="lg:col-span-2">
                            <p class="text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Add Server Embeds (automatically add embed links using TMDB ID):
                            </p>
                            <label class="flex items-center gap-2 cursor-pointer mb-2">
                                <input type="checkbox" name="embed_servers[]" value="vidsrc" 
                                       class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent dark:!bg-bg-card-hover dark:!border-border-primary">
                                <span class="text-sm font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                    Vidsrc.icu
                                </span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="embed_servers[]" value="vidlink" 
                                       class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent dark:!bg-bg-card-hover dark:!border-border-primary">
                                <span class="text-sm font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                    Vidlink.pro
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 dark:!text-text-secondary mt-1 ml-6" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                For movies: Adds watch link. For TV shows: Adds embed servers for all episodes when created. You can select both services.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="px-6 py-3 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Create Article Content
                        </button>
                        <button type="button" onclick="resetArticleForm()" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Reset
                        </button>
                        <a href="{{ route('admin.contents.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom Content Tab -->
    <div id="panel-custom" class="tab-panel hidden">
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
            <form action="{{ route('admin.contents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title') }}" required
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
                                    <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Description</label>
                            <textarea name="description" rows="5"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Poster URL</label>
                            <input type="text" name="poster_path" value="{{ old('poster_path') }}" placeholder="https://..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @error('poster_path')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Backdrop URL</label>
                            <input type="text" name="backdrop_path" value="{{ old('backdrop_path') }}" placeholder="https://..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @error('backdrop_path')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Release Date</label>
                            <input type="date" name="release_date" value="{{ old('release_date') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @error('release_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Rating (0-10)</label>
                            <input type="number" name="rating" value="{{ old('rating') }}" min="0" max="10" step="0.1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @error('rating')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Status</label>
                            <select name="status" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="upcoming" {{ old('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Dubbing Language</label>
                            <select name="dubbing_language"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                                <option value="">Select Language</option>
                                @foreach($dubbingLanguages as $lang => $label)
                                    <option value="{{ $lang }}" {{ old('dubbing_language') === $lang ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('dubbing_language')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Watch Link</label>
                            <input type="url" name="watch_link" value="{{ old('watch_link') }}" placeholder="https://..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @error('watch_link')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Download Link</label>
                            <input type="url" name="download_link" value="{{ old('download_link') }}" placeholder="https://..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                            @error('download_link')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <input type="hidden" name="content_type" value="custom">
                        <input type="hidden" name="sort_order" value="0">
                        <input type="hidden" name="is_featured" value="0">
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Create Content
                    </button>
                    <a href="{{ route('admin.contents.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tab switching
function switchTab(tab) {
    // Hide all panels
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.add('hidden');
    });
    
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('text-accent', 'border-accent');
        button.classList.add('text-gray-600', 'border-transparent', 'dark:!text-text-secondary');
    });
    
    // Show selected panel
    document.getElementById('panel-' + tab).classList.remove('hidden');
    
    // Activate selected tab
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('text-gray-600', 'border-transparent', 'dark:!text-text-secondary');
    activeTab.classList.add('text-accent', 'border-accent');
}

// TMDB Search
document.getElementById('tmdb-search-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const query = document.getElementById('tmdb-query').value.trim();
    const type = document.getElementById('tmdb-type').value;
    
    if (!query) {
        alert('Please enter a search query');
        return;
    }
    
    const loading = document.getElementById('tmdb-loading');
    const results = document.getElementById('tmdb-results');
    const resultsList = document.getElementById('tmdb-results-list');
    
    loading.classList.remove('hidden');
    results.classList.add('hidden');
    resultsList.innerHTML = '';
    
    try {
        const response = await fetch(`{{ route('admin.contents.tmdb.search') }}?q=${encodeURIComponent(query)}&type=${type}`);
        const data = await response.json();
        
        loading.classList.add('hidden');
        
        if (data.results && data.results.length > 0) {
            resultsList.innerHTML = data.results.map(item => {
                const posterUrl = item.poster_path 
                    ? `{{ config('services.tmdb.image_base_url') }}/w185${item.poster_path}`
                    : 'https://via.placeholder.com/185x278?text=No+Image';
                const title = item.title || item.name || 'Unknown';
                const date = item.release_date || item.first_air_date || 'N/A';
                
                return `
                    <div class="bg-gray-50 dark:!bg-bg-card-hover rounded-lg border border-gray-200 dark:!border-border-secondary p-4">
                        <div class="mb-3">
                            <img src="${posterUrl}" alt="${title}" class="w-full h-auto rounded" onerror="this.src='https://via.placeholder.com/185x278?text=No+Image'">
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:!text-white mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600;">${title}</h3>
                        <p class="text-sm text-gray-600 dark:!text-text-secondary mb-3" style="font-family: 'Poppins', sans-serif; font-weight: 400;">${date}</p>
                        <form action="{{ route('admin.contents.tmdb.import') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="tmdb_id" value="${item.id}">
                            <input type="hidden" name="type" value="${type}">
                            ${Array.from(document.querySelectorAll('input[name="embed_servers[]"]:checked')).map(checkbox => 
                                `<input type="hidden" name="embed_servers[]" value="${checkbox.value}">`
                            ).join('')}
                            <button type="submit" class="w-full px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Import
                            </button>
                        </form>
                    </div>
                `;
            }).join('');
            
            results.classList.remove('hidden');
        } else {
            resultsList.innerHTML = '<p class="text-gray-600 dark:!text-text-secondary" style="font-family: \'Poppins\', sans-serif; font-weight: 400;">No results found.</p>';
            results.classList.remove('hidden');
        }
    } catch (error) {
        loading.classList.add('hidden');
        alert('Error searching TMDB. Please try again.');
        console.error(error);
    }
});

// Article Content Tab Functions
let selectedArticleContent = null;

// Article TMDB Search
document.getElementById('article-search-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const query = document.getElementById('article-tmdb-query').value.trim();
    const type = document.getElementById('article-tmdb-type').value;
    
    if (!query) {
        alert('Please enter a search query');
        return;
    }
    
    const loading = document.getElementById('article-loading');
    const results = document.getElementById('article-results');
    const resultsList = document.getElementById('article-results-list');
    
    loading.classList.remove('hidden');
    results.classList.add('hidden');
    resultsList.innerHTML = '';
    
    try {
        const response = await fetch(`{{ route('admin.contents.tmdb.search') }}?q=${encodeURIComponent(query)}&type=${type}`);
        const data = await response.json();
        
        loading.classList.add('hidden');
        
        if (data.results && data.results.length > 0) {
            resultsList.innerHTML = data.results.map(item => {
                const posterUrl = item.poster_path 
                    ? `{{ config('services.tmdb.image_base_url') }}/w185${item.poster_path}`
                    : 'https://via.placeholder.com/185x278?text=No+Image';
                const backdropUrl = item.backdrop_path 
                    ? `{{ config('services.tmdb.image_base_url') }}/w780${item.backdrop_path}`
                    : 'https://via.placeholder.com/780x439?text=No+Backdrop';
                const title = item.title || item.name || 'Unknown';
                const date = item.release_date || item.first_air_date || 'N/A';
                
                return `
                    <div class="bg-gray-50 dark:!bg-bg-card-hover rounded-lg border border-gray-200 dark:!border-border-secondary p-4 cursor-pointer hover:border-accent transition-colors" onclick="selectArticleContent(${item.id}, '${type}', ${JSON.stringify(item).replace(/"/g, '&quot;')})">
                        <div class="mb-3">
                            <img src="${posterUrl}" alt="${title}" class="w-full h-auto rounded" onerror="this.src='https://via.placeholder.com/185x278?text=No+Image'">
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:!text-white mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600;">${title}</h3>
                        <p class="text-sm text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">${date}</p>
                        <div class="mt-2 text-xs text-accent" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Click to select →
                        </div>
                    </div>
                `;
            }).join('');
            
            results.classList.remove('hidden');
        } else {
            resultsList.innerHTML = '<p class="text-gray-600 dark:!text-text-secondary" style="font-family: \'Poppins\', sans-serif; font-weight: 400;">No results found.</p>';
            results.classList.remove('hidden');
        }
    } catch (error) {
        loading.classList.add('hidden');
        alert('Error searching TMDB. Please try again.');
        console.error(error);
    }
});

// Select Article Content
function selectArticleContent(tmdbId, type, item) {
    selectedArticleContent = item;
    
    // Set hidden form fields
    document.getElementById('article-tmdb-id').value = tmdbId;
    document.getElementById('article-tmdb-type-value').value = type;
    
    // Show preview
    const previewContainer = document.getElementById('article-preview-content');
    const posterUrl = item.poster_path 
        ? `{{ config('services.tmdb.image_base_url') }}/w185${item.poster_path}`
        : 'https://via.placeholder.com/185x278?text=No+Image';
    const backdropUrl = item.backdrop_path 
        ? `{{ config('services.tmdb.image_base_url') }}/w1280${item.backdrop_path}`
        : 'https://via.placeholder.com/1280x720?text=No+Backdrop';
    const title = item.title || item.name || 'Unknown';
    const date = item.release_date || item.first_air_date || 'N/A';
    const overview = item.overview || 'No description available.';
    
    previewContainer.innerHTML = `
        <div class="flex-shrink-0">
            <img src="${posterUrl}" alt="${title}" class="w-32 h-auto rounded" onerror="this.src='https://via.placeholder.com/128x192?text=No+Image'">
        </div>
        <div class="flex-1">
            <h4 class="text-xl font-bold text-gray-900 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 700;">${title}</h4>
            <p class="text-sm text-gray-600 dark:!text-text-secondary mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Release Date: ${date}</p>
            <p class="text-sm text-gray-700 dark:!text-text-secondary mb-3" style="font-family: 'Poppins', sans-serif; font-weight: 400;">${overview.substring(0, 200)}${overview.length > 200 ? '...' : ''}</p>
            <div class="mb-3">
                <p class="text-xs text-gray-500 dark:!text-text-secondary mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Backdrop Image:</p>
                <img src="${backdropUrl}" alt="Backdrop" class="w-full h-32 object-cover rounded" onerror="this.src='https://via.placeholder.com/800x450?text=No+Backdrop'">
            </div>
        </div>
    `;
    
    // Show form container
    document.getElementById('article-form-container').classList.remove('hidden');
    
    // Scroll to form
    document.getElementById('article-form-container').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Reset Article Form
function resetArticleForm() {
    selectedArticleContent = null;
    document.getElementById('article-tmdb-query').value = '';
    document.getElementById('article-tmdb-id').value = '';
    document.getElementById('article-tmdb-type-value').value = '';
    document.getElementById('article-content-text').value = '';
    document.getElementById('article-watch-link').value = '';
    document.getElementById('article-download-link').value = '';
    document.getElementById('article-results').classList.add('hidden');
    document.getElementById('article-form-container').classList.add('hidden');
    document.getElementById('article-preview-content').innerHTML = '';
}
</script>
@endsection

