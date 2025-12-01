<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class CommentController extends Controller
{
    /**
     * Store a new comment.
     */
    public function store(Request $request)
    {
        // Rate limiting: max 5 comments per 15 minutes per IP
        $key = 'comment:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many comments. Please wait a few minutes before commenting again.',
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'content_id' => 'required|exists:contents,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'comment' => 'required|string|min:3|max:5000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if content exists
        $content = Content::findOrFail($request->content_id);

        // Check if parent comment exists and belongs to same content
        if ($request->parent_id) {
            $parent = Comment::where('id', $request->parent_id)
                ->where('content_id', $request->content_id)
                ->first();
            
            if (!$parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parent comment not found.',
                ], 404);
            }

            // Prevent too deep nesting (max 3 levels)
            $parentDepth = $parent->calculateDepth();
            if ($parentDepth >= 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum reply depth reached.',
                ], 422);
            }
        }

        // Basic spam detection
        $spamWords = ['spam', 'casino', 'poker', 'viagra', 'loan', 'credit'];
        $commentText = strtolower($request->comment);
        $isSpam = false;
        foreach ($spamWords as $word) {
            if (str_contains($commentText, $word)) {
                $isSpam = true;
                break;
            }
        }

        $comment = Comment::create([
            'content_id' => $request->content_id,
            'name' => $request->name,
            'email' => $request->email,
            'comment' => $request->comment,
            'parent_id' => $request->parent_id,
            'status' => $isSpam ? 'spam' : 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        RateLimiter::hit($key, 900); // 15 minutes

        return response()->json([
            'success' => true,
            'message' => $isSpam 
                ? 'Your comment has been submitted and will be reviewed.' 
                : 'Your comment has been submitted and is pending approval.',
            'comment' => $comment,
        ], 201);
    }

    /**
     * Get comments for a content.
     */
    public function getComments(Request $request, $contentId)
    {
        try {
            // Validate contentId
            if (!is_numeric($contentId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid content ID.',
                ], 400);
            }
            
            $content = Content::find($contentId);
            
            if (!$content) {
                return response()->json([
                    'success' => false,
                    'message' => 'Content not found.',
                ], 404);
            }
            
            $comments = Comment::where('content_id', $contentId)
                ->topLevel()
                ->approved()
                ->with(['replies' => function($query) {
                    $query->approved()
                        ->with(['replies' => function($subQuery) {
                            $subQuery->approved();
                        }])
                        ->orderBy('created_at', 'asc');
                }])
                ->orderBy('is_pinned', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'comments' => $comments,
                'count' => $comments->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading comments: ' . $e->getMessage(), [
                'contentId' => $contentId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load comments.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Like a comment.
     */
    public function like(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        
        // Simple like system (can be enhanced with user tracking)
        $comment->increment('likes');

        return response()->json([
            'success' => true,
            'likes' => $comment->likes,
        ]);
    }

    /**
     * Dislike a comment.
     */
    public function dislike(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        
        $comment->increment('dislikes');

        return response()->json([
            'success' => true,
            'dislikes' => $comment->dislikes,
        ]);
    }
}
