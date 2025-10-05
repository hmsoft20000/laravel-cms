<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;





use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\PageMeta\PageMeta;


class PageMetaPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any page meta.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('pages.view') || $this->authService->hasPermission('settings.view');
    }

    /**
     * Determine whether the user can view the page meta.
     */
    public function view(CmsUserInterface $user, PageMeta $pageMeta): bool
    {
        return $this->authService->hasPermission('pages.view') || $this->authService->hasPermission('settings.view');
    }

    /**
     * Determine whether the user can create page meta.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('pages.edit') || $this->authService->hasPermission('settings.edit');
    }

    /**
     * Determine whether the user can update the page meta.
     */
    public function update(CmsUserInterface $user, PageMeta $pageMeta): bool
    {
        return $this->authService->hasPermission('pages.edit') || $this->authService->hasPermission('settings.edit');
    }

    /**
     * Determine whether the user can delete the page meta.
     */
    public function delete(CmsUserInterface $user, PageMeta $pageMeta): bool
    {
        // Only super admins and system admins can delete page meta
        return $user->hasAnyRole(['super-admin', 'system-admin']);
    }

    /**
     * Determine whether the user can manage SEO settings.
     */
    public function manageSEO(CmsUserInterface $user, PageMeta $pageMeta): bool
    {
        return $this->authService->hasPermission('pages.edit') || $this->authService->hasPermission('settings.edit');
    }

    /**
     * Determine whether the user can manage Open Graph meta tags.
     */
    public function manageOpenGraph(CmsUserInterface $user, PageMeta $pageMeta): bool
    {
        return $this->authService->hasPermission('pages.edit') || $this->authService->hasPermission('settings.edit');
    }

    /**
     * Determine whether the user can manage Twitter Card meta tags.
     */
    public function manageTwitterCard(CmsUserInterface $user, PageMeta $pageMeta): bool
    {
        return $this->authService->hasPermission('pages.edit') || $this->authService->hasPermission('settings.edit');
    }

    /**
     * Determine whether the user can manage structured data.
     */
    public function manageStructuredData(CmsUserInterface $user, PageMeta $pageMeta): bool
    {
        return $this->authService->hasPermission('pages.edit') || $this->authService->hasPermission('settings.edit');
    }

    /**
     * Determine whether the user can bulk update page meta.
     */
    public function bulkUpdate(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('pages.edit') || $this->authService->hasPermission('settings.edit');
    }

    /**
     * Determine whether the user can export page meta data.
     */
    public function export(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('pages.view') || $this->authService->hasPermission('settings.view');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(CmsUserInterface $user, PageMeta $pageMeta): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(CmsUserInterface $user, PageMeta $pageMeta): bool
    {
        return false;
    }
}
