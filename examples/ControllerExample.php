<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use HMsoft\Cms\Models\Content\Post;

class PostController extends Controller
{
    /**
     * Display a listing of posts
     */
    public function index(Request $request): JsonResponse
    {
        // The user is automatically available via $request->user() 
        // thanks to cms.auth middleware
        
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Get posts based on user permissions
        $query = Post::query();
        
        // If user is not admin, only show published posts or user's own posts
        if (!$user->hasRole('admin')) {
            $query->where(function($q) use ($user) {
                $q->where('is_published', true)
                  ->orWhere('user_id', $user->id);
            });
        }

        $posts = $query->paginate(10);

        return response()->json([
            'posts' => $posts,
            'user' => $user
        ]);
    }

    /**
     * Store a newly created post
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Check if user has permission to create posts
        if (!$user->hasPermissionTo('create-posts')) {
            return response()->json(['error' => 'Insufficient permissions'], 403);
        }

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => $user->id,
            'is_published' => $request->is_published ?? false,
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }

    /**
     * Display the specified post
     */
    public function show(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Check if user can view this post
        if (!$user->can('view', $post)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        return response()->json(['post' => $post]);
    }

    /**
     * Update the specified post
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Check if user can update this post
        if (!$user->can('update', $post)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $post->update($request->only(['title', 'content', 'is_published']));

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ]);
    }

    /**
     * Remove the specified post
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Check if user can delete this post
        if (!$user->can('delete', $post)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    /**
     * Get user's own posts
     */
    public function myPosts(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $posts = Post::where('user_id', $user->id)->paginate(10);

        return response()->json([
            'posts' => $posts,
            'user' => $user
        ]);
    }
}
