<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;
use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Content\Blog;


class BlogPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any blogs.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('blogs.view');
    }

    /**
     * Determine whether the user can view the blog.
     */
    public function view(CmsUserInterface $user, Blog $blog): bool
    {
        if (!$user || $this->authService->isAuthenticated() === false) {
            return $blog->is_active;
        }

        if (!$this->authService->hasPermission('blogs.view')) {
            return false;
        }

        if (!$blog->is_active && !$this->authService->hasPermission('blogs.edit')) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create blogs.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('blogs.create');
    }

    /**
     * Determine whether the user can update the blog.
     */
    public function update(CmsUserInterface $user, Blog $blog): bool
    {
        if (!$this->authService->hasPermission('blogs.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('blogs.publish')) {
            return $blog->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the blog.
     */
    public function delete(CmsUserInterface $user, Blog $blog): bool
    {
        if (!$this->authService->hasPermission('blogs.delete')) {
            return false;
        }

        if (!$this->authService->hasPermission('blogs.publish')) {
            return $blog->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can publish/unpublish the blog.
     */
    public function publish(CmsUserInterface $user, Blog $blog): bool
    {
        return $this->authService->hasPermission('blogs.publish');
    }

    /**
     * Determine whether the user can manage media for the blog.
     */
    public function manageMedia(CmsUserInterface $user, Blog $blog): bool
    {
        if (!$this->authService->hasPermission('blogs.manage-media')) {
            return false;
        }

        if (!$this->authService->hasPermission('blogs.publish')) {
            return $blog->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can restore the blog.
     */
    public function restore(CmsUserInterface $user, Blog $blog): bool
    {
        return $this->authService->hasPermission('blogs.delete');
    }

    /**
     * Determine whether the user can permanently delete the blog.
     */
    public function forceDelete(CmsUserInterface $user, Blog $blog): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can view unpublished blogs.
     */
    public function viewUnpublished(CmsUserInterface $user): bool
    {
        if (!$user || $this->authService->isAuthenticated() === false) {
            return false;
        }

        return $this->authService->hasPermission('blogs.edit') || $this->authService->hasPermission('blogs.publish');
    }

    /**
     * Determine whether the user can view blog analytics.
     */
    public function viewAnalytics(CmsUserInterface $user, Blog $blog): bool
    {
        if ($blog->user_id === $this->authService->getUserId()) {
            return true;
        }

        return $user->hasAnyRole(['super-admin', 'admin', 'editor']);
    }
}
