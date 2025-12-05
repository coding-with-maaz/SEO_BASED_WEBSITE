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
            @if($content->tmdb_id)
                <button onclick="showImportEpisodesModal()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Import from TMDB
                </button>
            @endif
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
                                <button onclick="editServer(this, ${server.id})" data-server='${JSON.stringify(server).replace(/'/g, "&#39;")}' class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors edit-server-btn" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Edit</button>
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

function editServer(button, serverId) {
    // Validate server ID is numeric
    const numericId = parseInt(serverId);
    if (!numericId || isNaN(numericId) || numericId <= 0) {
        console.error('Invalid server ID:', serverId);
        alert('Invalid server ID. Please refresh the page and try again.');
        return;
    }
    
    // Get server data from button's data attribute
    const serverDataAttr = button.getAttribute('data-server');
    
    if (!serverDataAttr) {
        alert('Server data not found. Please refresh the page and try again.');
        return;
    }
    
    let server;
    try {
        // Decode HTML entities
        const decodedData = serverDataAttr.replace(/&#39;/g, "'").replace(/&quot;/g, '"');
        server = JSON.parse(decodedData);
    } catch (e) {
        console.error('Error parsing server data:', e, serverDataAttr);
        alert('Error loading server data. Please refresh the page.');
        return;
    }
    
    // Populate form with server data
    const nameInput = document.querySelector('[name="server_name"]');
    const qualityInput = document.querySelector('[name="quality"]');
    const watchLinkInput = document.querySelector('[name="watch_link"]');
    const downloadLinkInput = document.querySelector('[name="download_link"]');
    const sortOrderInput = document.querySelector('[name="sort_order"]');
    const isActiveInput = document.querySelector('[name="is_active"]');
    
    // Verify server has server_name before populating
    if (!server || !server.server_name) {
        console.error('Error: Server data missing server_name', server);
        alert('Error loading server data. The server name is missing.');
        return;
    }
    
    if (nameInput) {
        const serverNameValue = String(server.server_name || '').trim();
        if (!serverNameValue) {
            console.error('Warning: server_name is empty in server data', server);
            alert('Warning: Server name is empty. Please enter a server name.');
        }
        nameInput.value = serverNameValue;
        // Ensure the field is visible and focused for editing
        setTimeout(() => nameInput.focus(), 100);
    } else {
        console.error('Error: server_name input field not found in form');
        alert('Error: Server name field not found. Please refresh the page.');
        return;
    }
    
    if (qualityInput) qualityInput.value = String(server.quality || '').trim();
    if (watchLinkInput) watchLinkInput.value = String(server.watch_link || '').trim();
    if (downloadLinkInput) downloadLinkInput.value = String(server.download_link || '').trim();
    if (sortOrderInput) sortOrderInput.value = server.sort_order || 0;
    if (isActiveInput) {
        isActiveInput.checked = server.is_active === true || server.is_active === 1 || server.is_active === '1';
    }
    
    // Verify server_name was populated correctly
    const finalValue = nameInput.value.trim();
    if (!finalValue) {
        console.error('Error: server_name is empty after populating form', {
            server: server,
            nameInputValue: nameInput.value,
            serverName: server.server_name
        });
        alert('Error: Server name is empty. Please enter a server name before saving.');
        nameInput.focus();
    }
    
    // Change form to update mode
    const form = document.getElementById('addServerForm');
    if (form) {
        form.dataset.mode = 'edit';
        form.dataset.serverId = numericId.toString();
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.textContent = 'Update Server';
        }
    }
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
    const mode = this.dataset.mode;
    const serverId = this.dataset.serverId;
    
    // Get form values manually to ensure they're captured
    const serverNameInput = this.querySelector('[name="server_name"]');
    const qualityInput = this.querySelector('[name="quality"]');
    const watchLinkInput = this.querySelector('[name="watch_link"]');
    const downloadLinkInput = this.querySelector('[name="download_link"]');
    const sortOrderInput = this.querySelector('[name="sort_order"]');
    const isActiveCheckbox = this.querySelector('[name="is_active"]');
    
    // Validate required fields
    const serverName = serverNameInput ? serverNameInput.value.trim() : '';
    if (!serverName) {
        alert('Server name is required.');
        if (serverNameInput) {
            serverNameInput.focus();
        }
        return;
    }
    
    // Validate server ID if in edit mode
    if (mode === 'edit' && (!serverId || serverId.includes(':'))) {
        alert('Invalid server ID. Please refresh the page and try again.');
        console.error('Invalid server ID:', serverId);
        return;
    }
    
    // Build form data manually to ensure all values are included
    const formData = new FormData();
    
    // Always include server_name (required field) - ensure it's not empty
    if (!serverName || !serverName.trim()) {
        alert('Server name is required and cannot be empty.');
        if (serverNameInput) {
            serverNameInput.focus();
        }
        return;
    }
    formData.append('server_name', serverName.trim());
    
    // Include optional fields
    if (qualityInput) {
        const qualityValue = (qualityInput.value || '').trim();
        if (qualityValue) {
            formData.append('quality', qualityValue);
        }
    }
    if (watchLinkInput) {
        const watchLinkValue = (watchLinkInput.value || '').trim();
        if (watchLinkValue) {
            formData.append('watch_link', watchLinkValue);
        }
    }
    if (downloadLinkInput) {
        const downloadLinkValue = (downloadLinkInput.value || '').trim();
        if (downloadLinkValue) {
            formData.append('download_link', downloadLinkValue);
        }
    }
    if (sortOrderInput) {
        formData.append('sort_order', sortOrderInput.value || '0');
    }
    if (isActiveCheckbox) {
        formData.append('is_active', isActiveCheckbox.checked ? '1' : '0');
    }
    
    const url = mode === 'edit' 
        ? `/admin/contents/{{ $content->slug }}/episodes/${currentEpisodeId}/servers/${serverId}`
        : `/admin/contents/{{ $content->slug }}/episodes/${currentEpisodeId}/servers`;
    
    // For PUT requests, Laravel requires method spoofing
    if (mode === 'edit') {
        formData.append('_method', 'PUT');
    }
    
    // Log all form data entries for debugging
    const formDataObj = {};
    for (let [key, value] of formData.entries()) {
        formDataObj[key] = value;
    }
    
    console.log('Submitting server form:', { 
        mode, 
        serverId, 
        url, 
        method: mode === 'edit' ? 'POST (with _method=PUT)' : 'POST',
        formData: formDataObj,
        serverNameInput: serverNameInput ? serverNameInput.value : 'not found'
    });
    
    // Always use POST method, Laravel will handle method spoofing via _method field
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(async response => {
        const responseData = await response.json().catch(() => ({}));
        
        if (!response.ok) {
            // Show validation errors if available
            let errorMessage = responseData.message || 'Error saving server.';
            if (responseData.errors) {
                const errorMessages = Object.values(responseData.errors).flat();
                errorMessage = errorMessages.join('\n');
            }
            
            console.error('Server error response:', {
                status: response.status,
                statusText: response.statusText,
                data: responseData,
                url: url
            });
            
            throw new Error(errorMessage);
        }
        
        return responseData;
    })
    .then(data => {
        loadEpisodeServers(currentEpisodeId);
        this.reset();
        this.dataset.mode = '';
        this.dataset.serverId = '';
        this.querySelector('button[type="submit"]').textContent = 'Add Server';
        if (isActiveCheckbox) {
            isActiveCheckbox.checked = true;
        }
        alert(mode === 'edit' ? 'Server updated successfully!' : 'Server added successfully!');
    })
    .catch(error => {
        console.error('Error saving server:', error);
        alert(error.message || 'Error saving server. Please check the console for details.');
    });
});

// Close modal on outside click
document.getElementById('serverModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeServerModal();
    }
});

// Import Episodes Modal
function showImportEpisodesModal() {
    document.getElementById('importEpisodesModal').classList.remove('hidden');
}

function hideImportEpisodesModal() {
    document.getElementById('importEpisodesModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('importEpisodesModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideImportEpisodesModal();
    }
});

// Update selected season info
function updateSelectedSeason(seasonNumber, episodeCount) {
    const infoDiv = document.getElementById('selectedSeasonInfo');
    const seasonText = document.getElementById('selectedSeasonText');
    const episodeInfoText = document.getElementById('episodeInfoText');
    
    if (infoDiv && seasonText && episodeInfoText) {
        infoDiv.classList.remove('hidden');
        seasonText.textContent = `Season ${seasonNumber}`;
        episodeInfoText.textContent = `Will import all ${episodeCount} episode(s) from this season. Existing episodes with the same episode number will be automatically skipped.`;
    }
}
</script>

<!-- Import Episodes Modal -->
@if($content->tmdb_id)
<div id="importEpisodesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:!bg-bg-card rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Import Episodes from TMDB
                </h3>
                <button onclick="hideImportEpisodesModal()" class="text-gray-500 hover:text-gray-700 dark:!text-text-secondary dark:!hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            @if(isset($seasons) && count($seasons) > 0)
            <form action="{{ route('admin.contents.episodes.import-from-tmdb', $content) }}" method="POST" id="importSeasonForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-3" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Select Season to Import <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="space-y-2 max-h-[400px] overflow-y-auto">
                            @foreach($seasons as $season)
                                @php
                                    $seasonNumber = $season['season_number'] ?? 0;
                                    $episodeCount = $season['episode_count'] ?? 0;
                                    $seasonName = $season['name'] ?? "Season {$seasonNumber}";
                                    $airDate = $season['air_date'] ?? null;
                                    $posterPath = $season['poster_path'] ?? null;
                                    
                                    // Show total existing episodes for reference
                                    $totalExisting = $episodes->count();
                                @endphp
                                <label class="flex items-start gap-4 p-4 border-2 border-gray-200 dark:!border-border-primary rounded-lg cursor-pointer hover:border-accent transition-colors group">
                                    <input type="radio" name="season_number" value="{{ $seasonNumber }}" required
                                           class="mt-1 w-5 h-5 text-accent border-gray-300 focus:ring-accent"
                                           onchange="updateSelectedSeason({{ $seasonNumber }}, {{ $episodeCount }})">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            @if($posterPath)
                                                <img src="{{ app(\App\Services\TmdbService::class)->getImageUrl($posterPath, 'w92') }}" 
                                                     alt="{{ $seasonName }}"
                                                     class="w-16 h-24 object-cover rounded flex-shrink-0">
                                            @else
                                                <div class="w-16 h-24 bg-gray-200 dark:!bg-gray-800 rounded flex items-center justify-center flex-shrink-0">
                                                    <span class="text-gray-400 text-xs">No Image</span>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <h4 class="text-lg font-bold text-gray-900 dark:!text-white group-hover:text-accent transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                                                    {{ $seasonName }}
                                                </h4>
                                                <div class="flex flex-wrap items-center gap-3 mt-1 text-sm text-gray-600 dark:!text-text-secondary">
                                                    <span style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                                        <span class="font-semibold">{{ $episodeCount }}</span> episode(s)
                                                    </span>
                                                    @if($airDate)
                                                        <span>•</span>
                                                        <span style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                                            {{ \Carbon\Carbon::parse($airDate)->format('Y') }}
                                                        </span>
                                                    @endif
                                                    @if($totalExisting > 0)
                                                        <span>•</span>
                                                        <span class="text-yellow-600 dark:!text-yellow-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                                            {{ $totalExisting }} total episode(s) already exist
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if(!empty($season['overview']))
                                            <p class="text-sm text-gray-600 dark:!text-text-secondary mt-2 line-clamp-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                                {{ $season['overview'] }}
                                            </p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 dark:!text-text-secondary mt-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            Select a season above to import all episodes from that season
                        </p>
                    </div>
                    
                    <div id="selectedSeasonInfo" class="hidden bg-blue-50 dark:!bg-blue-900/20 border border-blue-200 dark:!border-blue-800 rounded-lg p-4">
                        <p class="text-sm text-blue-800 dark:!text-blue-300" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            <strong>Selected Season:</strong> <span id="selectedSeasonText"></span><br>
                            <span id="episodeInfoText"></span>
                        </p>
                    </div>
                    
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Add Embed Servers to imported episodes:
                        </p>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="embed_servers[]" value="vidsrc" checked
                                   class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent">
                            <span class="text-sm font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Vidsrc.icu
                            </span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="embed_servers[]" value="vidlink" checked
                                   class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent">
                            <span class="text-sm font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                Vidlink.pro
                            </span>
                        </label>
                    </div>
                    
                    <div class="bg-yellow-50 dark:!bg-yellow-900/20 border border-yellow-200 dark:!border-yellow-800 rounded-lg p-4">
                        <p class="text-sm text-yellow-800 dark:!text-yellow-300" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            <strong>Note:</strong> This will import <strong>all episodes</strong> from the selected season. Existing episodes with the same episode number will be skipped. Episode images, titles, descriptions, and air dates will be automatically fetched from TMDB.
                        </p>
                    </div>
                </div>
                
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Import Whole Season
                    </button>
                    <button type="button" onclick="hideImportEpisodesModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card-hover dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Cancel
                    </button>
                </div>
            </form>
            @else
            <div class="space-y-4">
                <div class="bg-red-50 dark:!bg-red-900/20 border border-red-200 dark:!border-red-800 rounded-lg p-4">
                    <p class="text-sm text-red-800 dark:!text-red-300" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        <strong>Error:</strong> Could not fetch season information from TMDB. Please check the TMDB ID or try again later.
                    </p>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="hideImportEpisodesModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card-hover dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Close
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
@endsection

