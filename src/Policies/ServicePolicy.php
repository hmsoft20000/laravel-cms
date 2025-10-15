<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;
use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Content\Service;


class ServicePolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any services.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('services.view');
    }

    /**
     * Determine whether the user can view the service.
     */
    public function view(CmsUserInterface $user, Service $service): bool
    {
        if (!$user || $this->authService->isAuthenticated() === false) {
            return $service->is_active;
        }

        if (!$this->authService->hasPermission('services.view')) {
            return false;
        }

        if (!$service->is_active && !$this->authService->hasPermission('services.edit')) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create services.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('services.create');
    }

    /**
     * Determine whether the user can update the service.
     */
    public function update(CmsUserInterface $user, Service $service): bool
    {
        if (!$this->authService->hasPermission('services.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('services.publish')) {
            return $service->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the service.
     */
    public function delete(CmsUserInterface $user, Service $service): bool
    {
        if (!$this->authService->hasPermission('services.delete')) {
            return false;
        }

        if (!$this->authService->hasPermission('services.publish')) {
            return $service->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can publish/unpublish the service.
     */
    public function publish(CmsUserInterface $user, Service $service): bool
    {
        return $this->authService->hasPermission('services.publish');
    }

    /**
     * Determine whether the user can manage media for the service.
     */
    public function manageMedia(CmsUserInterface $user, Service $service): bool
    {
        if (!$this->authService->hasPermission('services.manage-media')) {
            return false;
        }

        if (!$this->authService->hasPermission('services.publish')) {
            return $service->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can restore the service.
     */
    public function restore(CmsUserInterface $user, Service $service): bool
    {
        return $this->authService->hasPermission('services.delete');
    }

    /**
     * Determine whether the user can permanently delete the service.
     */
    public function forceDelete(CmsUserInterface $user, Service $service): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can view unpublished services.
     */
    public function viewUnpublished(CmsUserInterface $user): bool
    {
        if (!$user || $this->authService->isAuthenticated() === false) {
            return false;
        }

        return $this->authService->hasPermission('services.edit') || $this->authService->hasPermission('services.publish');
    }

    /**
     * Determine whether the user can view service analytics.
     */
    public function viewAnalytics(CmsUserInterface $user, Service $service): bool
    {
        if ($service->user_id === $this->authService->getUserId()) {
            return true;
        }

        return $user->hasAnyRole(['super-admin', 'admin', 'editor']);
    }
}
