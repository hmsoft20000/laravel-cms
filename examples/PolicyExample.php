<?php

namespace App\Policies;

use HMsoft\Cms\Traits\General\HasPolicyAuth;
use HMsoft\Cms\Models\Content\Post;

class PostPolicy
{
    use HasPolicyAuth;

    /**
     * Determine whether the user can view any posts.
     */
    public function viewAny($user): bool
    {
        return $this->isPolicyUserAuthenticated();
    }

    /**
     * Determine whether the user can view the post.
     */
    public function view($user, Post $post): bool
    {
        // Check if user is authenticated
        if (!$this->isPolicyUserAuthenticated()) {
            return false;
        }

        // Check if user has permission to view posts
        if (!$this->policyUserHasPermission('view-posts')) {
            return false;
        }

        // Check if post is published or user is the author
        $currentUser = $this->getPolicyUser();
        return $post->is_published || $post->user_id === $currentUser->id;
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create($user): bool
    {
        return $this->isPolicyUserAuthenticated() && 
               $this->policyUserHasPermission('create-posts');
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update($user, Post $post): bool
    {
        if (!$this->isPolicyUserAuthenticated()) {
            return false;
        }

        $currentUser = $this->getPolicyUser();

        // Check if user is the author
        if ($post->user_id === $currentUser->id) {
            return true;
        }

        // Check if user has admin role or permission to edit all posts
        return $this->policyUserHasRole('admin') || 
               $this->policyUserHasPermission('edit-all-posts');
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete($user, Post $post): bool
    {
        if (!$this->isPolicyUserAuthenticated()) {
            return false;
        }

        $currentUser = $this->getPolicyUser();

        // Check if user is the author
        if ($post->user_id === $currentUser->id) {
            return true;
        }

        // Check if user has admin role or permission to delete all posts
        return $this->policyUserHasRole('admin') || 
               $this->policyUserHasPermission('delete-all-posts');
    }

    /**
     * Determine whether the user can restore the post.
     */
    public function restore($user, Post $post): bool
    {
        return $this->isPolicyUserAuthenticated() && 
               $this->policyUserHasPermission('restore-posts');
    }

    /**
     * Determine whether the user can permanently delete the post.
     */
    public function forceDelete($user, Post $post): bool
    {
        return $this->isPolicyUserAuthenticated() && 
               $this->policyUserHasRole('admin');
    }
}
