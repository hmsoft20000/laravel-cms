<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;



use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Shared\Feature;


class FeaturePolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any features.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Allow guest users to view features
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('features.view');
    }

    /**
     * Determine whether the user can view the feature.
     */
    public function view(CmsUserInterface $user, Feature $feature): bool
    {
        // Allow guest users to view features
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('features.view');
    }

    /**
     * Determine whether the user can create features.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('features.create');
    }

    /**
     * Determine whether the user can update the feature.
     */
    public function update(CmsUserInterface $user, Feature $feature): bool
    {
        if (!$this->authService->hasPermission('features.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('features.publish')) {
            return $feature->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the feature.
     */
    public function delete(CmsUserInterface $user, Feature $feature): bool
    {
        if (!$this->authService->hasPermission('features.delete')) {
            return false;
        }

        if (!$this->authService->hasPermission('features.publish')) {
            return $feature->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can publish/unpublish the feature.
     */
    public function publish(CmsUserInterface $user, Feature $feature): bool
    {
        return $this->authService->hasPermission('features.publish');
    }

    /**
     * Determine whether the user can reorder features.
     */
    public function reorder(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('features.edit');
    }

    /**
     * Determine whether the user can bulk update features.
     */
    public function bulkUpdate(CmsUserInterface $user): bool
    {

        return $this->authService->hasPermission('features.edit');
    }

    /**
     * Determine whether the user can manage media for features.
     */
    public function manageMedia(CmsUserInterface $user, Feature $feature): bool
    {
        if (!$this->authService->hasPermission('features.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('features.publish')) {
            return $feature->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(CmsUserInterface $user, Feature $feature): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(CmsUserInterface $user, Feature $feature): bool
    {
        return false;
    }
}
