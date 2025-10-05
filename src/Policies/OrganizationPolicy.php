<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;



use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Organizations\Organization;


class OrganizationPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any organizations.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Allow guest users to view organizations
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('organizations.view');
    }

    /**
     * Determine whether the user can view the organization.
     */
    public function view(CmsUserInterface $user, Organization $organization): bool
    {
        // Allow guest users to view organizations
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('organizations.view');
    }

    /**
     * Determine whether the user can create organizations.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('organizations.create');
    }

    /**
     * Determine whether the user can update the organization.
     */
    public function update(CmsUserInterface $user, Organization $organization): bool
    {
        if (!$this->authService->hasPermission('organizations.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('organizations.publish')) {
            return $organization->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the organization.
     */
    public function delete(CmsUserInterface $user, Organization $organization): bool
    {
        if (!$this->authService->hasPermission('organizations.delete')) {
            return false;
        }

        if (!$this->authService->hasPermission('organizations.publish')) {
            return $organization->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can publish/unpublish the organization.
     */
    public function publish(CmsUserInterface $user, Organization $organization): bool
    {
        return $this->authService->hasPermission('organizations.publish');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(CmsUserInterface $user, Organization $organization): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(CmsUserInterface $user, Organization $organization): bool
    {
        return false;
    }
}
