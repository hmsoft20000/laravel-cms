<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;





use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Sector\Sector;

use Illuminate\Auth\Access\Response;

class SectorPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any sectors.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Allow guest users to view sectors
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('sectors.view');
    }

    /**
     * Determine whether the user can view the sector.
     */
    public function view(CmsUserInterface $user, Sector $sector): bool
    {
        // Allow guest users to view sectors
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('sectors.view');
    }

    /**
     * Determine whether the user can create sectors.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('sectors.create');
    }

    /**
     * Determine whether the user can update the sector.
     */
    public function update(CmsUserInterface $user, Sector $sector): bool
    {
        if (!$this->authService->hasPermission('sectors.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('sectors.publish')) {
            return $sector->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the sector.
     */
    public function delete(CmsUserInterface $user, Sector $sector): bool
    {
        if (!$this->authService->hasPermission('sectors.delete')) {
            return false;
        }

        if (!$this->authService->hasPermission('sectors.publish')) {
            return $sector->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can publish/unpublish the sector.
     */
    public function publish(CmsUserInterface $user, Sector $sector): bool
    {
        return $this->authService->hasPermission('sectors.publish');
    }

    /**
     * Determine whether the user can manage images for the sector.
     */
    public function manageImages(CmsUserInterface $user, Sector $sector): bool
    {
        if (!$this->authService->hasPermission('sectors.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('sectors.publish')) {
            return $sector->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(CmsUserInterface $user, Sector $sector): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(CmsUserInterface $user, Sector $sector): bool
    {
        return false;
    }
}
