<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;



use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Shared\Category;

class CategoryPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any categories.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Allow guest users to view categories
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('categories.view');
    }

    /**
     * Determine whether the user can view the category.
     */
    public function view(CmsUserInterface $user, Category $category): bool
    {
        // Allow guest users to view categories
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('categories.view');
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('categories.create');
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update(CmsUserInterface $user, Category $category): bool
    {
        if (!$this->authService->hasPermission('categories.edit')) {
            return false;
        }

        // Authors can only edit their own categories (unless they have publish permission)
        if (!$this->authService->hasPermission('categories.publish')) {
            return $category->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function delete(CmsUserInterface $user, Category $category): bool
    {
        if (!$this->authService->hasPermission('categories.delete')) {
            return false;
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return false; // Cannot delete category with children
        }

        // Authors can only delete their own categories (unless they have publish permission)
        if (!$this->authService->hasPermission('categories.publish')) {
            return $category->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can publish/unpublish the category.
     */
    public function publish(CmsUserInterface $user, Category $category): bool
    {
        return $this->authService->hasPermission('categories.publish');
    }

    /**
     * Determine whether the user can reorder categories.
     */
    public function reorder(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('categories.edit');
    }

    /**
     * Determine whether the user can manage category hierarchy.
     */
    public function manageHierarchy(CmsUserInterface $user, Category $category): bool
    {
        return $this->authService->hasPermission('categories.edit');
    }

    /**
     * Determine whether the user can restore the category.
     */
    public function restore(CmsUserInterface $user, Category $category): bool
    {
        return $this->authService->hasPermission('categories.delete');
    }

    /**
     * Determine whether the user can permanently delete the category.
     */
    public function forceDelete(CmsUserInterface $user, Category $category): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }
}
