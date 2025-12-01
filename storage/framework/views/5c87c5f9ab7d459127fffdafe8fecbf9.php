<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['contentId']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['contentId']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(!isset($contentId) || !$contentId): ?>
    <?php
        // Don't render comments if contentId is not provided
        return;
    ?>
<?php endif; ?>

<div class="mt-12" id="comments-section">
    <div class="bg-white dark:!bg-bg-card border border-gray-200 dark:!border-border-secondary rounded-lg p-6 md:p-8">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:!text-white mb-6" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Comments
        </h2>

        <!-- Comment Form -->
        <div class="mb-8">
            <form id="commentForm" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="content_id" value="<?php echo e($contentId); ?>">
                <input type="hidden" name="parent_id" id="reply_to_id" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="comment_name" class="block text-sm font-semibold text-gray-900 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="comment_name" 
                               name="name" 
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 dark:!border-border-primary rounded-lg bg-white dark:!bg-bg-card-hover text-gray-900 dark:!text-white focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all"
                               style="font-family: 'Poppins', sans-serif; font-weight: 400;"
                               placeholder="Your name">
                    </div>
                    <div>
                        <label for="comment_email" class="block text-sm font-semibold text-gray-900 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="comment_email" 
                               name="email" 
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 dark:!border-border-primary rounded-lg bg-white dark:!bg-bg-card-hover text-gray-900 dark:!text-white focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all"
                               style="font-family: 'Poppins', sans-serif; font-weight: 400;"
                               placeholder="your.email@example.com">
                    </div>
                </div>
                
                <div>
                    <label for="comment_text" class="block text-sm font-semibold text-gray-900 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Comment <span class="text-red-500">*</span>
                    </label>
                    <textarea id="comment_text" 
                              name="comment" 
                              rows="4" 
                              required
                              class="w-full px-4 py-2.5 border border-gray-300 dark:!border-border-primary rounded-lg bg-white dark:!bg-bg-card-hover text-gray-900 dark:!text-white focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all resize-none"
                              style="font-family: 'Poppins', sans-serif; font-weight: 400;"
                              placeholder="Write your comment here..."></textarea>
                </div>
                
                <div id="reply_to_info" class="hidden bg-blue-50 dark:!bg-blue-900/20 border border-blue-200 dark:!border-blue-800 rounded-lg p-3 mb-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-blue-800 dark:!text-blue-300" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                            Replying to: <span id="reply_to_name"></span>
                        </span>
                        <button type="button" onclick="cancelReply()" class="text-blue-600 dark:!text-blue-400 hover:text-blue-800 dark:!hover:text-blue-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <button type="submit" 
                        id="submitComment"
                        class="px-6 py-3 bg-accent hover:bg-accent-light text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                        style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    <span id="submitText">Post Comment</span>
                    <span id="submitLoading" class="hidden">Posting...</span>
                </button>
            </form>
        </div>

        <!-- Comments List -->
        <div id="commentsList" class="space-y-6">
            <div class="text-center py-8 text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Loading comments...
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let currentReplyTo = null;

// Load comments on page load
document.addEventListener('DOMContentLoaded', function() {
    const contentId = <?php echo e($contentId ?? 'null'); ?>;
    
    if (!contentId) {
        console.error('Content ID is missing');
        const commentsList = document.getElementById('commentsList');
        if (commentsList) {
            commentsList.innerHTML = 
                '<div class="text-center py-8 text-gray-500 dark:!text-text-tertiary" style="font-family: \'Poppins\', sans-serif; font-weight: 400;">Comments are not available for this content.</div>';
        }
        return;
    }
    
    console.log('Loading comments for content ID:', contentId);
    loadComments(contentId);
    loadSavedCommentData();
    
    // Handle comment form submission
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitComment();
        });
    }
    
    // Save name and email to local storage as user types
    const nameInput = document.getElementById('comment_name');
    const emailInput = document.getElementById('comment_email');
    
    if (nameInput) {
        nameInput.addEventListener('input', function() {
            localStorage.setItem('comment_name', this.value);
        });
    }
    
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            localStorage.setItem('comment_email', this.value);
        });
    }
});

// Load saved name and email from local storage
function loadSavedCommentData() {
    const savedName = localStorage.getItem('comment_name');
    const savedEmail = localStorage.getItem('comment_email');
    
    if (savedName) {
        document.getElementById('comment_name').value = savedName;
    }
    
    if (savedEmail) {
        document.getElementById('comment_email').value = savedEmail;
    }
}

function loadComments(contentId) {
    const commentsList = document.getElementById('commentsList');
    
    if (!commentsList) {
        console.error('Comments list element not found');
        return;
    }
    
    if (!contentId || contentId === 'null' || contentId === null) {
        console.error('Invalid content ID:', contentId);
        commentsList.innerHTML = 
            '<div class="text-center py-8 text-gray-500 dark:!text-text-tertiary" style="font-family: \'Poppins\', sans-serif; font-weight: 400;">Comments are not available for this content.</div>';
        return;
    }
    
    console.log('Fetching comments for content ID:', contentId);
    
    // Use AbortController to handle cleanup
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
    
    fetch(`/comments/${contentId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        signal: controller.signal,
        credentials: 'same-origin'
    })
        .then(async response => {
            clearTimeout(timeoutId);
            console.log('Response status:', response.status);
            
            // Check if response is actually a Response object
            if (!response || typeof response.json !== 'function') {
                throw new Error('Invalid response received');
            }
            
            if (!response.ok) {
                // Try to parse error response as JSON first, then as text
                let errorMessage = `HTTP error! status: ${response.status}`;
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // If JSON parsing fails, try text
                    try {
                        const errorText = await response.text();
                        if (errorText) {
                            try {
                                const parsed = JSON.parse(errorText);
                                errorMessage = parsed.message || errorMessage;
                            } catch (parseError) {
                                // Not JSON, use status code
                                if (response.status === 404) {
                                    errorMessage = 'Content not found.';
                                } else if (response.status === 500) {
                                    errorMessage = 'Server error. Please try again later.';
                                }
                            }
                        }
                    } catch (textError) {
                        // Can't read response, use status code
                        if (response.status === 404) {
                            errorMessage = 'Content not found.';
                        } else if (response.status === 500) {
                            errorMessage = 'Server error. Please try again later.';
                        }
                    }
                }
                throw new Error(errorMessage);
            }
            
            // Response is OK, parse as JSON
            try {
                return await response.json();
            } catch (jsonError) {
                console.error('Failed to parse JSON response:', jsonError);
                throw new Error('Invalid response format from server');
            }
        })
        .then(data => {
            console.log('Comments data received:', data);
            if (data && data.success) {
                const comments = data.comments || [];
                console.log('Comments count:', comments.length);
                if (comments.length === 0) {
                    commentsList.innerHTML = 
                        '<div class="text-center py-8 text-gray-500 dark:!text-text-tertiary" style="font-family: \'Poppins\', sans-serif; font-weight: 400;">No comments yet. Be the first to comment!</div>';
                } else {
                    displayComments(comments);
                }
            } else {
                console.warn('Unexpected response format:', data);
                commentsList.innerHTML = 
                    '<div class="text-center py-8 text-gray-500 dark:!text-text-tertiary" style="font-family: \'Poppins\', sans-serif; font-weight: 400;">No comments yet. Be the first to comment!</div>';
            }
        })
        .catch(error => {
            clearTimeout(timeoutId);
            // Ignore AbortError (timeout or manual abort)
            if (error.name === 'AbortError') {
                console.warn('Request aborted or timed out');
                commentsList.innerHTML = 
                    '<div class="text-center py-8 text-gray-500 dark:!text-text-tertiary" style="font-family: \'Poppins\', sans-serif; font-weight: 400;">Request timeout. Please refresh the page.</div>';
                return;
            }
            // Only log non-abort errors
            console.error('Error loading comments:', error);
            commentsList.innerHTML = 
                '<div class="text-center py-8 text-gray-500 dark:!text-text-tertiary" style="font-family: \'Poppins\', sans-serif; font-weight: 400;">' + (error.message || 'Failed to load comments. Please refresh the page.') + '</div>';
        });
}

function displayComments(comments) {
    const container = document.getElementById('commentsList');
    
    if (!container) {
        console.error('Comments container not found');
        return;
    }
    
    console.log('Displaying comments:', comments);
    
    if (!comments || !Array.isArray(comments) || comments.length === 0) {
        console.log('No comments to display');
        container.innerHTML = '<div class="text-center py-8 text-gray-500 dark:!text-text-tertiary" style="font-family: \'Poppins\', sans-serif; font-weight: 400;">No comments yet. Be the first to comment!</div>';
        return;
    }
    
    try {
        console.log('Rendering', comments.length, 'comments');
        const html = comments.map(comment => renderComment(comment, 0)).join('');
        container.innerHTML = html;
        console.log('Comments rendered successfully');
    } catch (error) {
        console.error('Error rendering comments:', error, error.stack);
        container.innerHTML = '<div class="text-center py-8 text-gray-500 dark:!text-text-tertiary" style="font-family: \'Poppins\', sans-serif; font-weight: 400;">Error displaying comments. Please refresh the page.</div>';
    }
}

function renderComment(comment, depth) {
    if (!comment || !comment.id) {
        console.error('Invalid comment data:', comment);
        return '';
    }
    
    const indent = depth * 2;
    const maxDepth = 3;
    const canReply = depth < maxDepth;
    
    let repliesHtml = '';
    if (comment.replies && Array.isArray(comment.replies) && comment.replies.length > 0) {
        repliesHtml = '<div class="mt-4 ml-8 space-y-4">' + 
            comment.replies.map(reply => renderComment(reply, depth + 1)).join('') + 
            '</div>';
    }
    
    const timeAgo = comment.created_at ? getTimeAgo(comment.created_at) : 'recently';
    const commentName = comment.name || 'Anonymous';
    const commentText = comment.comment || '';
    const commentLikes = comment.likes || 0;
    const commentDislikes = comment.dislikes || 0;
    const isPinned = comment.is_pinned || false;
    
    return `
        <div class="comment-item border-b border-gray-200 dark:!border-border-secondary pb-6 last:border-0" data-comment-id="${comment.id}" style="margin-left: ${indent}rem;">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-accent to-red-700 flex items-center justify-center text-white font-bold" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                        ${commentName.charAt(0).toUpperCase()}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <h4 class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">${escapeHtml(commentName)}</h4>
                        ${isPinned ? '<span class="px-2 py-0.5 bg-accent text-white text-xs rounded">Pinned</span>' : ''}
                        <span class="text-sm text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">${timeAgo}</span>
                    </div>
                    <p class="text-gray-700 dark:!text-text-secondary mb-3 whitespace-pre-wrap" style="font-family: 'Poppins', sans-serif; font-weight: 400;">${escapeHtml(commentText)}</p>
                    <div class="flex items-center gap-4">
                        <button onclick="likeComment(${comment.id})" class="flex items-center gap-1 text-gray-600 dark:!text-text-secondary hover:text-accent transition-colors text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v3.667c0 .368.098.73.284 1.044l2.571 4.352A2 2 0 0010.632 16H12"></path>
                            </svg>
                            <span id="likes-${comment.id}">${commentLikes}</span>
                        </button>
                        <button onclick="dislikeComment(${comment.id})" class="flex items-center gap-1 text-gray-600 dark:!text-text-secondary hover:text-red-600 transition-colors text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .966-.35 1.237-.864l4.018-6.033a2 2 0 00.447-1.302V9a2 2 0 00-2-2h-3.382"></path>
                            </svg>
                            <span id="dislikes-${comment.id}">${commentDislikes}</span>
                        </button>
                        ${canReply ? `<button onclick="replyToComment(${comment.id}, '${escapeHtml(commentName)}')" class="text-gray-600 dark:!text-text-secondary hover:text-accent transition-colors text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Reply</button>` : ''}
                    </div>
                </div>
            </div>
            ${repliesHtml}
        </div>
    `;
}

function submitComment() {
    const form = document.getElementById('commentForm');
    if (!form) {
        console.error('Comment form not found');
        return;
    }
    
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitComment');
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');
    
    if (!submitBtn || !submitText || !submitLoading) {
        console.error('Comment form elements not found');
        return;
    }
    
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');
    
    // Use AbortController for cleanup
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
    
    fetch('/comments', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json'
        },
        signal: controller.signal,
        credentials: 'same-origin'
    })
    .then(async response => {
        clearTimeout(timeoutId);
        
        // Check if response is actually a Response object
        if (!response || typeof response.json !== 'function') {
            throw new Error('Invalid response received');
        }
        
        if (!response.ok) {
            // Try to parse error response as JSON first, then as text
            let errorMessage = `HTTP error! status: ${response.status}`;
            try {
                const errorData = await response.json();
                errorMessage = errorData.message || errorMessage;
            } catch (e) {
                // If JSON parsing fails, try text
                try {
                    const errorText = await response.text();
                    if (errorText) {
                        try {
                            const parsed = JSON.parse(errorText);
                            errorMessage = parsed.message || errorMessage;
                        } catch (parseError) {
                            // Not JSON, use status code
                            if (response.status === 422) {
                                errorMessage = 'Validation failed. Please check your input.';
                            } else if (response.status === 429) {
                                errorMessage = 'Too many requests. Please wait a moment.';
                            } else if (response.status === 500) {
                                errorMessage = 'Server error. Please try again later.';
                            }
                        }
                    }
                } catch (textError) {
                    // Can't read response, use status code
                    if (response.status === 422) {
                        errorMessage = 'Validation failed. Please check your input.';
                    } else if (response.status === 429) {
                        errorMessage = 'Too many requests. Please wait a moment.';
                    } else if (response.status === 500) {
                        errorMessage = 'Server error. Please try again later.';
                    }
                }
            }
            throw new Error(errorMessage);
        }
        
        // Response is OK, parse as JSON
        try {
            return await response.json();
        } catch (jsonError) {
            console.error('Failed to parse JSON response:', jsonError);
            throw new Error('Invalid response format from server');
        }
    })
    .then(data => {
        if (data && data.success) {
            // Save name and email to local storage
            const nameInput = document.getElementById('comment_name');
            const emailInput = document.getElementById('comment_email');
            if (nameInput && emailInput) {
                localStorage.setItem('comment_name', nameInput.value);
                localStorage.setItem('comment_email', emailInput.value);
            }
            
            // Reset only the comment text and reply fields, keep name and email
            const commentText = document.getElementById('comment_text');
            if (commentText) {
                commentText.value = '';
            }
            cancelReply();
            const contentId = <?php echo e($contentId ?? 'null'); ?>;
            if (contentId) {
                loadComments(contentId);
            }
            alert(data.message || 'Comment submitted successfully!');
        } else {
            alert(data?.message || 'Failed to submit comment. Please try again.');
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        // Ignore AbortError
        if (error.name === 'AbortError') {
            alert('Request timeout. Please try again.');
            return;
        }
        console.error('Error:', error);
        alert(error.message || 'An error occurred. Please try again.');
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
        }
        if (submitText) {
            submitText.classList.remove('hidden');
        }
        if (submitLoading) {
            submitLoading.classList.add('hidden');
        }
    });
}

function replyToComment(commentId, userName) {
    currentReplyTo = commentId;
    document.getElementById('reply_to_id').value = commentId;
    document.getElementById('reply_to_name').textContent = userName;
    document.getElementById('reply_to_info').classList.remove('hidden');
    document.getElementById('comment_text').focus();
    
    // Scroll to form
    document.getElementById('commentForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function cancelReply() {
    currentReplyTo = null;
    document.getElementById('reply_to_id').value = '';
    document.getElementById('reply_to_info').classList.add('hidden');
}

function likeComment(commentId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        console.error('CSRF token not found');
        return;
    }
    
    fetch(`/comments/${commentId}/like`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(async response => {
        if (!response || typeof response.json !== 'function') {
            throw new Error('Invalid response received');
        }
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        try {
            return await response.json();
        } catch (jsonError) {
            console.error('Failed to parse JSON response:', jsonError);
            throw new Error('Invalid response format from server');
        }
    })
    .then(data => {
        if (data && data.success) {
            const likesElement = document.getElementById(`likes-${commentId}`);
            if (likesElement) {
                likesElement.textContent = data.likes || 0;
            }
        }
    })
    .catch(error => {
        if (error.name !== 'AbortError') {
            console.error('Error liking comment:', error);
        }
    });
}

function dislikeComment(commentId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        console.error('CSRF token not found');
        return;
    }
    
    fetch(`/comments/${commentId}/dislike`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(async response => {
        if (!response || typeof response.json !== 'function') {
            throw new Error('Invalid response received');
        }
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        try {
            return await response.json();
        } catch (jsonError) {
            console.error('Failed to parse JSON response:', jsonError);
            throw new Error('Invalid response format from server');
        }
    })
    .then(data => {
        if (data && data.success) {
            const dislikesElement = document.getElementById(`dislikes-${commentId}`);
            if (dislikesElement) {
                dislikesElement.textContent = data.dislikes || 0;
            }
        }
    })
    .catch(error => {
        if (error.name !== 'AbortError') {
            console.error('Error disliking comment:', error);
        }
    });
}

function getTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'just now';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' minutes ago';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
    if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + ' days ago';
    if (diffInSeconds < 2592000) return Math.floor(diffInSeconds / 604800) + ' weeks ago';
    if (diffInSeconds < 31536000) return Math.floor(diffInSeconds / 2592000) + ' months ago';
    return Math.floor(diffInSeconds / 31536000) + ' years ago';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
<?php $__env->stopPush(); ?>

<?php /**PATH C:\Users\k\Desktop\Nazaarabox\resources\views/components/comments.blade.php ENDPATH**/ ?>