<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;





use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Content\Post;


class PostPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any posts.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Allow guest users to view published posts
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true; // Guest can view posts
        }

        return $this->authService->hasPermission('posts.view');
    }

    /**
     * Determine whether the user can view the post.
     */
    public function view(CmsUserInterface $user, Post $post): bool
    {
        // Allow guest users to view published posts
        if (!$user || $this->authService->isAuthenticated() === false) {
            return $post->is_active; // Guest can only view published posts
        }

        // Check if user has view permission
        if (!$this->authService->hasPermission('posts.view')) {
            return false;
        }

        // If post is unpublished, user needs edit permission
        if (!$post->is_active && !$this->authService->hasPermission('posts.edit')) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('posts.create');
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update(CmsUserInterface $user, Post $post): bool
    {
        // Check basic edit permission
        if (!$this->authService->hasPermission('posts.edit')) {
            return false;
        }

        // Authors can only edit their own posts (unless they have publish permission)
        if (!$this->authService->hasPermission('posts.publish')) {
            // Check if user is the author (assuming user_id field exists)
            return $post->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(CmsUserInterface $user, Post $post): bool
    {
        // Check basic delete permission
        if (!$this->authService->hasPermission('posts.delete')) {
            return false;
        }

        // Authors can only delete their own posts (unless they have publish permission)
        if (!$this->authService->hasPermission('posts.publish')) {
            return $post->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can publish/unpublish the post.
     */
    public function publish(CmsUserInterface $user, Post $post): bool
    {
        return $this->authService->hasPermission('posts.publish');
    }

    /**
     * Determine whether the user can manage media for the post.
     */
    public function manageMedia(CmsUserInterface $user, Post $post): bool
    {
        // Check basic media permission
        if (!$this->authService->hasPermission('posts.manage-media')) {
            return false;
        }

        // Authors can only manage media for their own posts (unless they have publish permission)
        if (!$this->authService->hasPermission('posts.publish')) {
            return $post->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can restore the post.
     */
    public function restore(CmsUserInterface $user, Post $post): bool
    {
        return $this->authService->hasPermission('posts.delete'); // Same permission as delete for restore
    }

    /**
     * Determine whether the user can permanently delete the post.
     */
    public function forceDelete(CmsUserInterface $user, Post $post): bool
    {
        // Only super admins and admins can force delete
        return $user->hasAnyRole(['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can view unpublished posts.
     */
    public function viewUnpublished(CmsUserInterface $user): bool
    {
        // Guest users cannot view unpublished posts
        if (!$user || $this->authService->isAuthenticated() === false) {
            return false;
        }

        return $this->authService->hasPermission('posts.edit') || $this->authService->hasPermission('posts.publish');
    }

    /**
     * Determine whether the user can view post analytics.
     */
    public function viewAnalytics(CmsUserInterface $user, Post $post): bool
    {
        // Authors can view analytics for their own posts
        if ($post->user_id === $this->authService->getUserId()) {
            return true;
        }

        // Admins and editors can view all analytics
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']);
    }
}
