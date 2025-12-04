@extends('layouts.app')

@section('title', 'Manage Episodes - ' . $content->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Manage Episodes - {{ $content->title }}
            </h1>
            <p class="text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Add and manage episodes for this TV show
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.contents.edit', $content) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Back to Content
            </a>
            <a href="{{ route('admin.episodes.create', $content) }}" class="px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Add Episode
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg dark:!bg-green-900/20 dark:!border-green-700 dark:!text-green-400">
        {{ session('success') }}
    </div>
    @endif

    @if($episodes->count() > 0)
    <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:!bg-bg-card-hover">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Episode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Servers</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:!divide-border-secondary">
                    @foreach($episodes as $episode)
                    <tr class="hover:bg-gray-50 dark:!hover:bg-bg-card-hover">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                E{{ $episode->episode_number }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                {{ $episode->title }}
                            </div>
                            @if($episode->description)
                            <div class="text-xs text-gray-500 dark:!text-text-tertiary mt-1 line-clamp-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                {{ Str::limit($episode->description, 100) }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                {{ $episode->servers->count() }} server(s)
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($episode->is_published)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:!bg-green-900/20 dark:!text-green-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Published</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:!bg-gray-800 dark:!text-gray-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('admin.episodes.edit', [$content, $episode]) }}" class="text-blue-600 hover:text-blue-900 dark:!text-blue-400 dark:!hover:text-blue-300" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Edit</a>
                                <button onclick="showServerModal({{ $episode->id }}, '{{ $episode->title }}')" class="text-purple-600 hover:text-purple-900 dark:!text-purple-400 dark:!hover:text-purple-300" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Servers</button>
                                <form action="{{ route('admin.episodes.destroy', [$content, $episode]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this episode?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:!text-red-400 dark:!hover:text-red-300" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-12 text-center">
        <p class="text-gray-600 dark:!text-text-secondary text-lg mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 400;">No episodes added yet.</p>
        <a href="{{ route('admin.episodes.create', $content) }}" class="inline-block px-6 py-3 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            Add First Episode
        </a>
    </div>
    @endif
</div>

<!-- Server Management Modal -->
<div id="serverModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:!bg-bg-card rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Manage Servers - <span id="modalEpisodeTitle"></span>
                </h3>
                <button onclick="closeServerModal()" class="text-gray-500 hover:text-gray-700 dark:!text-text-secondary dark:!hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="serverList" class="space-y-4 mb-6">
                <!-- Servers will be loaded here -->
            </div>

            <!-- Add Server Form -->
            <div class="border-t border-gray-200 dark:!border-border-secondary pt-6">
                <h4 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Add New Server</h4>
                <form id="addServerForm" class="space-y-4">
                    @csrf
                    <input type="hidden" id="modalEpisodeId" name="episode_id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Sort Order</label>
                            <input type="number" name="sort_order" value="0" min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                        </div>
                        <div class="flex items-center gap-2 mt-6">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                   class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent">
                            <label for="is_active" class="text-sm font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Active</label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeServerModal()" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Add Server
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentEpisodeId = null;

function showServerModal(episodeId, episodeTitle) {
    currentEpisodeId = episodeId;
    document.getElementById('modalEpisodeId').value = episodeId;
    document.getElementById('modalEpisodeTitle').textContent = episodeTitle;
    document.getElementById('serverModal').classList.remove('hidden');
    loadEpisodeServers(episodeId);
}

function closeServerModal() {
    document.getElementById('serverModal').classList.add('hidden');
    document.getElementById('serverList').innerHTML = '';
    const form = document.getElementById('addServerForm');
    form.reset();
    form.dataset.mode = '';
    form.dataset.serverId = '';
    form.querySelector('button[type="submit"]').textContent = 'Add Server';
    currentEpisodeId = null;
}

function loadEpisodeServers(episodeId) {
    fetch(`/admin/contents/{{ $content->slug }}/episodes/${episodeId}/servers`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response is not JSON');
            }
            return response.json();
        })
        .then(data => {
            const serverList = document.getElementById('serverList');
            if (data.servers && data.servers.length > 0) {
                serverList.innerHTML = data.servers.map(server => `
                    <div class="border border-gray-200 dark:!border-border-secondary rounded-lg p-4 bg-gray-50 dark:!bg-bg-card-hover">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h5 class="text-lg font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                        ${server.server_name}
                                    </h5>
                                    ${server.quality ? `<span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded dark:!bg-blue-900/20 dark:!text-blue-400">${server.quality}</span>` : ''}
                                    ${server.is_active ? '<span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded dark:!bg-green-900/20 dark:!text-green-400">Active</span>' : '<span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded dark:!bg-red-900/20 dark:!text-red-400">Inactive</span>'}
                                </div>
                                ${server.watch_link ? `<p class="text-sm text-gray-600 dark:!text-text-secondary mb-1"><span class="font-semibold">Watch:</span> <a href="${server.watch_link}" target="_blank" class="text-accent hover:underline break-all">${server.watch_link.substring(0, 60)}...</a></p>` : ''}
                                ${server.download_link ? `<p class="text-sm text-gray-600 dark:!text-text-secondary"><span class="font-semibold">Download:</span> <a href="${server.download_link}" target="_blank" class="text-accent hover:underline break-all">${server.download_link.substring(0, 60)}...</a></p>` : ''}
                            </div>
                            <div class="flex gap-2 ml-4">
                                <button onclick="editServer(${server.id}, ${JSON.stringify(server).replace(/"/g, '&quot;').replace(/'/g, '&#39;')})" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Edit</button>
                                <button onclick="deleteServer(${server.id})" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Delete</button>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                serverList.innerHTML = '<p class="text-gray-600 dark:!text-text-secondary text-center py-8">No servers added yet.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading servers:', error);
            document.getElementById('serverList').innerHTML = '<p class="text-red-600 text-center py-8">Error loading servers. Please refresh the page and try again.</p>';
        });
}

function editServer(serverId, serverData) {
    // Parse server data if it's a string
    const server = typeof serverData === 'string' ? JSON.parse(serverData.replace(/&quot;/g, '"')) : serverData;
    
    // Populate form with server data
    document.querySelector('[name="server_name"]').value = server.server_name || '';
    document.querySelector('[name="quality"]').value = server.quality || '';
    document.querySelector('[name="watch_link"]').value = server.watch_link || '';
    document.querySelector('[name="download_link"]').value = server.download_link || '';
    document.querySelector('[name="sort_order"]').value = server.sort_order || 0;
    document.querySelector('[name="is_active"]').checked = server.is_active || false;
    
    // Change form to update mode
    const form = document.getElementById('addServerForm');
    form.dataset.mode = 'edit';
    form.dataset.serverId = serverId;
    form.querySelector('button[type="submit"]').textContent = 'Update Server';
}

function deleteServer(serverId) {
    if (!confirm('Are you sure you want to delete this server?')) return;
    
    fetch(`/admin/contents/{{ $content->slug }}/episodes/${currentEpisodeId}/servers/${serverId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (response.ok) {
            return response.json().catch(() => ({ success: true }));
        }
        return response.json().then(data => {
            throw new Error(data.message || 'Error deleting server.');
        }).catch(() => {
            throw new Error('Error deleting server.');
        });
    })
    .then(data => {
        loadEpisodeServers(currentEpisodeId);
        alert('Server deleted successfully!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Error deleting server.');
    });
}

document.getElementById('addServerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const mode = this.dataset.mode;
    const serverId = this.dataset.serverId;
    
    const url = mode === 'edit' 
        ? `/admin/contents/{{ $content->slug }}/episodes/${currentEpisodeId}/servers/${serverId}`
        : `/admin/contents/{{ $content->slug }}/episodes/${currentEpisodeId}/servers`;
    
    const method = mode === 'edit' ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => {
        if (response.ok) {
            return response.json().catch(() => ({ success: true }));
        }
        return response.json().then(data => {
            throw new Error(data.message || 'Error saving server.');
        }).catch(() => {
            throw new Error('Error saving server.');
        });
    })
    .then(data => {
        loadEpisodeServers(currentEpisodeId);
        this.reset();
        this.dataset.mode = '';
        this.dataset.serverId = '';
        this.querySelector('button[type="submit"]').textContent = 'Add Server';
        alert(mode === 'edit' ? 'Server updated successfully!' : 'Server added successfully!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Error saving server.');
    });
});

// Close modal on outside click
document.getElementById('serverModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeServerModal();
    }
});
</script>
@endsection

