@extends('layouts.app')

@section('title', 'Admin - Comments Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-accent dark:!text-text-secondary dark:!hover:text-accent transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    ← Dashboard
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Comments Management
            </h1>
            <p class="text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Manage and moderate all comments
            </p>
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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-4">
            <div class="text-sm font-semibold text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Total</div>
            <div class="text-2xl font-bold text-gray-900 dark:!text-white mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-yellow-50 dark:!bg-yellow-900/20 rounded-lg border border-yellow-200 dark:!border-yellow-800 p-4">
            <div class="text-sm font-semibold text-yellow-700 dark:!text-yellow-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Pending</div>
            <div class="text-2xl font-bold text-yellow-800 dark:!text-yellow-300 mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($stats['pending']) }}</div>
        </div>
        <div class="bg-green-50 dark:!bg-green-900/20 rounded-lg border border-green-200 dark:!border-green-800 p-4">
            <div class="text-sm font-semibold text-green-700 dark:!text-green-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Approved</div>
            <div class="text-2xl font-bold text-green-800 dark:!text-green-300 mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($stats['approved']) }}</div>
        </div>
        <div class="bg-red-50 dark:!bg-red-900/20 rounded-lg border border-red-200 dark:!border-red-800 p-4">
            <div class="text-sm font-semibold text-red-700 dark:!text-red-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Rejected</div>
            <div class="text-2xl font-bold text-red-800 dark:!text-red-300 mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($stats['rejected']) }}</div>
        </div>
        <div class="bg-gray-50 dark:!bg-gray-900/20 rounded-lg border border-gray-200 dark:!border-gray-800 p-4">
            <div class="text-sm font-semibold text-gray-700 dark:!text-gray-400" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Spam</div>
            <div class="text-2xl font-bold text-gray-800 dark:!text-gray-300 mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ number_format($stats['spam']) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-4">
        <form method="GET" action="{{ route('admin.comments.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name, email, or comment..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
            </div>
            <div class="min-w-[150px]">
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                    <option value="">All Status</option>
                    <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="spam" {{ ($filters['status'] ?? '') === 'spam' ? 'selected' : '' }}>Spam</option>
                </select>
            </div>
            <div class="min-w-[200px]">
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Content</label>
                <select name="content_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                    <option value="">All Content</option>
                    @foreach($contents as $content)
                        <option value="{{ $content->id }}" {{ ($filters['content_id'] ?? '') == $content->id ? 'selected' : '' }}>{{ $content->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Sort By</label>
                <select name="sort_by" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                    <option value="created_at" {{ ($filters['sort_by'] ?? 'created_at') === 'created_at' ? 'selected' : '' }}>Date</option>
                    <option value="name" {{ ($filters['sort_by'] ?? '') === 'name' ? 'selected' : '' }}>Name</option>
                    <option value="likes" {{ ($filters['sort_by'] ?? '') === 'likes' ? 'selected' : '' }}>Likes</option>
                </select>
            </div>
            <div class="min-w-[120px]">
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Order</label>
                <select name="sort_order" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                    <option value="desc" {{ ($filters['sort_order'] ?? 'desc') === 'desc' ? 'selected' : '' }}>Desc</option>
                    <option value="asc" {{ ($filters['sort_order'] ?? '') === 'asc' ? 'selected' : '' }}>Asc</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    <form id="bulkActionForm" method="POST" action="{{ route('admin.comments.bulk-action') }}" class="mb-4">
        @csrf
        <div class="flex items-center gap-4">
            <select name="action" id="bulkAction" class="px-4 py-2 border border-gray-300 rounded-lg dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                <option value="">Bulk Actions</option>
                <option value="approve">Approve</option>
                <option value="reject">Reject</option>
                <option value="spam">Mark as Spam</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Apply
            </button>
        </div>
    </form>

    @if($comments->count() > 0)
    <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:!bg-bg-card-hover">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-accent focus:ring-accent">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Comment</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Content</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:!divide-border-secondary">
                    @foreach($comments as $comment)
                    <tr class="hover:bg-gray-50 dark:!hover:bg-bg-card-hover {{ $comment->status === 'pending' ? 'bg-yellow-50/50 dark:!bg-yellow-900/10' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" class="comment-checkbox rounded border-gray-300 text-accent focus:ring-accent">
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-md">
                                <div class="text-sm text-gray-900 dark:!text-white line-clamp-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                    {{ Str::limit($comment->comment, 100) }}
                                </div>
                                @if($comment->parent_id)
                                    <div class="text-xs text-gray-500 dark:!text-text-tertiary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                        Reply to: {{ $comment->parent->name ?? 'Unknown' }}
                                    </div>
                                @endif
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-xs text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                        👍 {{ $comment->likes }} 👎 {{ $comment->dislikes }}
                                    </span>
                                    @if($comment->is_pinned)
                                        <span class="px-2 py-0.5 bg-accent text-white text-xs rounded" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Pinned</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($comment->content)
                                <a href="{{ route('admin.contents.show', $comment->content) }}" class="text-sm text-accent hover:underline" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                    {{ Str::limit($comment->content->title, 30) }}
                                </a>
                            @else
                                <span class="text-sm text-gray-400" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Deleted</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                {{ $comment->name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                {{ $comment->email }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:!bg-yellow-900/20 dark:!text-yellow-400',
                                    'approved' => 'bg-green-100 text-green-800 dark:!bg-green-900/20 dark:!text-green-400',
                                    'rejected' => 'bg-red-100 text-red-800 dark:!bg-red-900/20 dark:!text-red-400',
                                    'spam' => 'bg-gray-100 text-gray-800 dark:!bg-gray-900/20 dark:!text-gray-400',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColors[$comment->status] ?? 'bg-gray-100 text-gray-800' }}" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                {{ ucfirst($comment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            {{ $comment->created_at->format('M d, Y') }}<br>
                            <span class="text-xs">{{ $comment->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.comments.show', $comment) }}" class="text-blue-600 hover:text-blue-800 dark:!text-blue-400 dark:!hover:text-blue-300" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @if($comment->status !== 'approved')
                                <form method="POST" action="{{ route('admin.comments.approve', $comment) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 dark:!text-green-400 dark:!hover:text-green-300" title="Approve">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @if($comment->status !== 'rejected')
                                <form method="POST" action="{{ route('admin.comments.reject', $comment) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:!text-red-400 dark:!hover:text-red-300" title="Reject">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @if($comment->status !== 'spam')
                                <form method="POST" action="{{ route('admin.comments.spam', $comment) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-gray-800 dark:!text-gray-400 dark:!hover:text-gray-300" title="Mark as Spam">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('admin.comments.pin', $comment) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-purple-600 hover:text-purple-800 dark:!text-purple-400 dark:!hover:text-purple-300" title="{{ $comment->is_pinned ? 'Unpin' : 'Pin' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                        </svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:!text-red-400 dark:!hover:text-red-300" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $comments->links() }}
    </div>
    @else
    <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-8 text-center">
        <p class="text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">No comments found.</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Select all checkbox
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.comment-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Bulk action form validation
    document.getElementById('bulkActionForm')?.addEventListener('submit', function(e) {
        const action = document.getElementById('bulkAction').value;
        const checked = document.querySelectorAll('.comment-checkbox:checked');
        
        if (!action) {
            e.preventDefault();
            alert('Please select an action.');
            return false;
        }
        
        if (checked.length === 0) {
            e.preventDefault();
            alert('Please select at least one comment.');
            return false;
        }
        
        if (action === 'delete' && !confirm(`Are you sure you want to delete ${checked.length} comment(s)?`)) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endpush
@endsection

