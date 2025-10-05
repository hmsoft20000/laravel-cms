<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;





use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Shared\Download;

use Illuminate\Auth\Access\Response;

class DownloadPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any downloads.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Allow guest users to view downloads
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('downloads.view');
    }

    /**
     * Determine whether the user can view the download.
     */
    public function view(CmsUserInterface $user, Download $download): bool
    {
        // Allow guest users to view downloads
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('downloads.view');
    }

    /**
     * Determine whether the user can create downloads.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('downloads.create');
    }

    /**
     * Determine whether the user can update the download.
     */
    public function update(CmsUserInterface $user, Download $download): bool
    {
        if (!$this->authService->hasPermission('downloads.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('downloads.publish')) {
            return $download->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the download.
     */
    public function delete(CmsUserInterface $user, Download $download): bool
    {
        if (!$this->authService->hasPermission('downloads.delete')) {
            return false;
        }

        if (!$this->authService->hasPermission('downloads.publish')) {
            return $download->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can download the file.
     */
    public function download(CmsUserInterface $user, Download $download): bool
    {
        return $this->authService->hasPermission('downloads.view');
    }

    /**
     * Determine whether the user can publish/unpublish the download.
     */
    public function publish(CmsUserInterface $user, Download $download): bool
    {
        return $this->authService->hasPermission('downloads.publish');
    }

    /**
     * Determine whether the user can bulk update downloads.
     */
    public function bulkUpdate(CmsUserInterface $user): bool
    {
        // Bulk update requires authentication
        if (!$user || $this->authService->isAuthenticated() === false) {
            return false;
        }

        return $this->authService->hasPermission('downloads.edit');
    }

    /**
     * Determine whether the user can manage files for downloads.
     */
    public function manageMedia(CmsUserInterface $user, Download $download): bool
    {
        if (!$this->authService->hasPermission('downloads.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('downloads.publish')) {
            return $download->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(CmsUserInterface $user, Download $download): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(CmsUserInterface $user, Download $download): bool
    {
        return false;
    }
}
