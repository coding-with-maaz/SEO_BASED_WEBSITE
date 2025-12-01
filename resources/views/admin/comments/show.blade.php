@extends('layouts.app')

@section('title', 'Admin - Comment Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.comments.index') }}" class="text-gray-600 hover:text-accent dark:!text-text-secondary dark:!hover:text-accent transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    ← Back to Comments
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Comment Details
            </h1>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg dark:!bg-green-900/20 dark:!border-green-700 dark:!text-green-400">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Comment -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Comment Card -->
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-accent to-red-700 flex items-center justify-center text-white font-bold text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                            {{ strtoupper(substr($comment->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                                {{ $comment->name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                {{ $comment->email }}
                            </p>
                        </div>
                    </div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 dark:!bg-yellow-900/20 dark:!text-yellow-400',
                            'approved' => 'bg-green-100 text-green-800 dark:!bg-green-900/20 dark:!text-green-400',
                            'rejected' => 'bg-red-100 text-red-800 dark:!bg-red-900/20 dark:!text-red-400',
                            'spam' => 'bg-gray-100 text-gray-800 dark:!bg-gray-900/20 dark:!text-gray-400',
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded text-sm font-semibold {{ $statusColors[$comment->status] ?? 'bg-gray-100 text-gray-800' }}" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        {{ ucfirst($comment->status) }}
                    </span>
                </div>

                <div class="mb-4">
                    <p class="text-gray-700 dark:!text-text-secondary whitespace-pre-wrap" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        {{ $comment->comment }}
                    </p>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:!border-border-secondary">
                    <div class="flex items-center gap-4 text-sm text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        <span>👍 {{ $comment->likes }}</span>
                        <span>👎 {{ $comment->dislikes }}</span>
                        @if($comment->is_pinned)
                            <span class="px-2 py-0.5 bg-accent text-white rounded text-xs" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Pinned</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        {{ $comment->created_at->format('F d, Y \a\t h:i A') }}
                    </div>
                </div>

                @if($comment->parent)
                <div class="mt-4 p-4 bg-gray-50 dark:!bg-bg-card-hover rounded-lg border-l-4 border-accent">
                    <p class="text-sm text-gray-600 dark:!text-text-secondary mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Replying to:
                    </p>
                    <p class="text-sm text-gray-700 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        {{ $comment->parent->name }}: {{ Str::limit($comment->parent->comment, 150) }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Replies -->
            @if($comment->replies && $comment->replies->count() > 0)
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Replies ({{ $comment->replies->count() }})
                </h3>
                <div class="space-y-4">
                    @foreach($comment->replies as $reply)
                    <div class="p-4 bg-gray-50 dark:!bg-bg-card-hover rounded-lg border-l-4 border-gray-300 dark:!border-border-primary">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                    {{ $reply->name }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                    {{ $reply->email }}
                                </p>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColors[$reply->status] ?? 'bg-gray-100 text-gray-800' }}" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                {{ ucfirst($reply->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 dark:!text-text-secondary mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            {{ $reply->comment }}
                        </p>
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            <span>{{ $reply->created_at->format('M d, Y h:i A') }}</span>
                            <span>👍 {{ $reply->likes }} 👎 {{ $reply->dislikes }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Actions
                </h3>
                <div class="space-y-2">
                    @if($comment->status !== 'approved')
                    <form method="POST" action="{{ route('admin.comments.approve', $comment) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-center rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Approve Comment
                        </button>
                    </form>
                    @endif
                    @if($comment->status !== 'rejected')
                    <form method="POST" action="{{ route('admin.comments.reject', $comment) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-center rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Reject Comment
                        </button>
                    </form>
                    @endif
                    @if($comment->status !== 'spam')
                    <form method="POST" action="{{ route('admin.comments.spam', $comment) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-center rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Mark as Spam
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('admin.comments.pin', $comment) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-center rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            {{ $comment->is_pinned ? 'Unpin' : 'Pin' }} Comment
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-center rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Delete Comment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Content Info -->
            @if($comment->content)
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Content
                </h3>
                <a href="{{ route('admin.contents.show', $comment->content) }}" class="block text-accent hover:underline mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    {{ $comment->content->title }}
                </a>
                <p class="text-sm text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    {{ ucfirst(str_replace('_', ' ', $comment->content->type)) }}
                </p>
                <a href="{{ $comment->content->type === 'movie' ? route('movies.show', $comment->content->slug ?? $comment->content->id) : route('tv-shows.show', $comment->content->slug ?? $comment->content->id) }}" target="_blank" class="mt-2 inline-block text-sm text-blue-600 hover:underline dark:!text-blue-400" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    View Public Page →
                </a>
            </div>
            @endif

            <!-- Comment Details -->
            <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                    Details
                </h3>
                <div class="space-y-3 text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    <div>
                        <span class="text-gray-500 dark:!text-text-tertiary">Comment ID:</span>
                        <span class="text-gray-900 dark:!text-white ml-2">#{{ $comment->id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:!text-text-tertiary">Created:</span>
                        <span class="text-gray-900 dark:!text-white ml-2">{{ $comment->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($comment->updated_at && $comment->updated_at != $comment->created_at)
                    <div>
                        <span class="text-gray-500 dark:!text-text-tertiary">Updated:</span>
                        <span class="text-gray-900 dark:!text-white ml-2">{{ $comment->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                    @if($comment->approved_at)
                    <div>
                        <span class="text-gray-500 dark:!text-text-tertiary">Approved:</span>
                        <span class="text-gray-900 dark:!text-white ml-2">{{ $comment->approved_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                    @if($comment->ip_address)
                    <div>
                        <span class="text-gray-500 dark:!text-text-tertiary">IP Address:</span>
                        <span class="text-gray-900 dark:!text-white ml-2">{{ $comment->ip_address }}</span>
                    </div>
                    @endif
                    @if($comment->user_agent)
                    <div>
                        <span class="text-gray-500 dark:!text-text-tertiary">User Agent:</span>
                        <span class="text-gray-900 dark:!text-white ml-2 text-xs break-all">{{ Str::limit($comment->user_agent, 50) }}</span>
                    </div>
                    @endif
                    @if($comment->parent_id)
                    <div>
                        <span class="text-gray-500 dark:!text-text-tertiary">Parent Comment:</span>
                        <a href="{{ route('admin.comments.show', $comment->parent_id) }}" class="text-accent hover:underline ml-2">View Parent</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

