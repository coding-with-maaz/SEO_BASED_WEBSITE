<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Content;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of comments.
     */
    public function index(Request $request)
    {
        $query = Comment::with(['content', 'parent']);

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('comment', 'like', '%' . $request->search . '%');
            });
        }

        // Content filter
        if ($request->has('content_id') && $request->content_id) {
            $query->where('content_id', $request->content_id);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $comments = $query->paginate(20);
        $contents = Content::published()->orderBy('title')->get();

        // Statistics
        $stats = [
            'total' => Comment::count(),
            'pending' => Comment::where('status', 'pending')->count(),
            'approved' => Comment::where('status', 'approved')->count(),
            'rejected' => Comment::where('status', 'rejected')->count(),
            'spam' => Comment::where('status', 'spam')->count(),
        ];

        return view('admin.comments.index', [
            'comments' => $comments,
            'contents' => $contents,
            'stats' => $stats,
            'filters' => $request->only(['status', 'search', 'content_id', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Show a specific comment.
     */
    public function show(Comment $comment)
    {
        $comment->load(['content', 'parent', 'replies']);
        
        return view('admin.comments.show', [
            'comment' => $comment,
        ]);
    }

    /**
     * Approve a comment.
     */
    public function approve(Comment $comment)
    {
        $comment->approve();

        return redirect()->back()
            ->with('success', 'Comment approved successfully.');
    }

    /**
     * Reject a comment.
     */
    public function reject(Comment $comment)
    {
        $comment->reject();

        return redirect()->back()
            ->with('success', 'Comment rejected successfully.');
    }

    /**
     * Mark comment as spam.
     */
    public function markAsSpam(Comment $comment)
    {
        $comment->markAsSpam();

        return redirect()->back()
            ->with('success', 'Comment marked as spam.');
    }

    /**
     * Pin/unpin a comment.
     */
    public function togglePin(Comment $comment)
    {
        $comment->update([
            'is_pinned' => !$comment->is_pinned,
        ]);

        return redirect()->back()
            ->with('success', $comment->is_pinned ? 'Comment pinned.' : 'Comment unpinned.');
    }

    /**
     * Bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,spam,delete',
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
        ]);

        $commentIds = $request->comment_ids;
        $action = $request->action;

        switch ($action) {
            case 'approve':
                Comment::whereIn('id', $commentIds)->get()->each->approve();
                $message = count($commentIds) . ' comment(s) approved.';
                break;
            case 'reject':
                Comment::whereIn('id', $commentIds)->update(['status' => 'rejected']);
                $message = count($commentIds) . ' comment(s) rejected.';
                break;
            case 'spam':
                Comment::whereIn('id', $commentIds)->update(['status' => 'spam']);
                $message = count($commentIds) . ' comment(s) marked as spam.';
                break;
            case 'delete':
                Comment::whereIn('id', $commentIds)->delete();
                $message = count($commentIds) . ' comment(s) deleted.';
                break;
        }

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Delete a comment.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->route('admin.comments.index')
            ->with('success', 'Comment deleted successfully.');
    }
}

