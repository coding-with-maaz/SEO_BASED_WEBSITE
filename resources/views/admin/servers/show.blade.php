@extends('layouts.app')

@section('title', 'Server Management - ' . $content->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.servers.index') }}" class="text-gray-600 hover:text-accent dark:!text-text-secondary dark:!hover:text-accent transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    ← Back to Server Management
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Server Management
            </h1>
            <p class="text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Manage servers for: <span class="font-semibold text-gray-900 dark:!text-white">{{ $content->title }}</span>
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.servers.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Back to List
            </a>
            <button onclick="showAddServerModal()" class="px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Add Server
            </button>
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

    <!-- Movie Info Card -->
    <div class="mb-6 bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
        <div class="flex gap-6">
            <div class="w-24 h-36 rounded overflow-hidden bg-gray-100 dark:!bg-bg-card-hover flex-shrink-0">
                @if($content->poster_path)
                    @php
                        $contentType = $content->content_type ?? 'custom';
                        if (str_starts_with($content->poster_path, 'http')) {
                            $posterUrl = $content->poster_path;
                        } elseif (in_array($contentType, ['tmdb', 'article']) || str_starts_with($content->poster_path, '/')) {
                            $posterUrl = app(\App\Services\TmdbService::class)->getImageUrl($content->poster_path, 'w185');
                        } else {
                            $posterUrl = asset('storage/' . $content->poster_path);
                        }
                    @endphp
                    <img src="{{ $posterUrl }}" alt="{{ $content->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Image</div>
                @endif
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    {{ $content->title }}
                </h2>
                @if($content->description)
                <p class="text-gray-600 dark:!text-text-secondary mb-4 line-clamp-3" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    {{ $content->description }}
                </p>
                @endif
                <div class="flex flex-wrap gap-4 text-sm">
                    @if($content->release_date)
                    <div>
                        <span class="text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Release Date:</span>
                        <span class="text-gray-900 dark:!text-white ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ $content->release_date->format('M d, Y') }}</span>
                    </div>
                    @endif
                    @if($content->rating)
                    <div>
                        <span class="text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Rating:</span>
                        <span class="text-gray-900 dark:!text-white ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ number_format($content->rating, 1) }}/10</span>
                    </div>
                    @endif
                    <div>
                        <span class="text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Total Servers:</span>
                        <span class="text-gray-900 dark:!text-white ml-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ count($servers) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Servers List -->
    <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
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
                                    @if(isset($server['quality']))
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
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded dark:!bg-gray-800 dark:!text-gray-400">
                                        Order: {{ $server['sort_order'] ?? $index }}
                                    </span>
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
                                <form action="{{ route('admin.servers.destroy', $content) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
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

<!-- Add Server Modal -->
<div id="add-server-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:!bg-bg-card rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Add New Server
        </h3>
        <form action="{{ route('admin.servers.store', $content) }}" method="POST">
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
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Watch URL (Embed Link) *</label>
                    <input type="url" name="watch_link" placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                    <p class="text-xs text-gray-500 dark:!text-text-tertiary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        The embed URL for the video player (e.g., iframe src)
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Download Link</label>
                    <input type="url" name="download_link" placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                    <p class="text-xs text-gray-500 dark:!text-text-tertiary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        Optional: Direct download link for this server
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ count($servers) }}" min="0"
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
        <form id="edit-server-form" action="{{ route('admin.servers.update', $content) }}" method="POST">
            @csrf
            @method('PUT')
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
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Watch URL (Embed Link) *</label>
                    <input type="url" id="edit-server-watch" name="watch_link" placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                    <p class="text-xs text-gray-500 dark:!text-text-tertiary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        The embed URL for the video player (e.g., iframe src)
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Download Link</label>
                    <input type="url" id="edit-server-download" name="download_link" placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                    <p class="text-xs text-gray-500 dark:!text-text-tertiary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        Optional: Direct download link for this server
                    </p>
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
// Modal functions
function showAddServerModal() {
    document.getElementById('add-server-modal').classList.remove('hidden');
}

function hideAddServerModal() {
    document.getElementById('add-server-modal').classList.add('hidden');
}

function showEditServerModal(server, index) {
    document.getElementById('edit-server-id').value = server.id || '';
    document.getElementById('edit-server-name').value = server.name || '';
    document.getElementById('edit-server-quality').value = server.quality || '';
    document.getElementById('edit-server-watch').value = server.url || '';
    document.getElementById('edit-server-download').value = server.download_link || '';
    document.getElementById('edit-server-order').value = server.sort_order ?? index;
    document.getElementById('edit-server-active').checked = (server.active !== false);
    
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

