<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;
use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Content\Portfolio;


class PortfolioPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any portfolios.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('portfolios.view');
    }

    /**
     * Determine whether the user can view the portfolio.
     */
    public function view(CmsUserInterface $user, Portfolio $portfolio): bool
    {
        if (!$user || $this->authService->isAuthenticated() === false) {
            return $portfolio->is_active;
        }

        if (!$this->authService->hasPermission('portfolios.view')) {
            return false;
        }

        if (!$portfolio->is_active && !$this->authService->hasPermission('portfolios.edit')) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create portfolios.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('portfolios.create');
    }

    /**
     * Determine whether the user can update the portfolio.
     */
    public function update(CmsUserInterface $user, Portfolio $portfolio): bool
    {
        if (!$this->authService->hasPermission('portfolios.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('portfolios.publish')) {
            return $portfolio->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the portfolio.
     */
    public function delete(CmsUserInterface $user, Portfolio $portfolio): bool
    {
        if (!$this->authService->hasPermission('portfolios.delete')) {
            return false;
        }

        if (!$this->authService->hasPermission('portfolios.publish')) {
            return $portfolio->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can publish/unpublish the portfolio.
     */
    public function publish(CmsUserInterface $user, Portfolio $portfolio): bool
    {
        return $this->authService->hasPermission('portfolios.publish');
    }

    /**
     * Determine whether the user can manage media for the portfolio.
     */
    public function manageMedia(CmsUserInterface $user, Portfolio $portfolio): bool
    {
        if (!$this->authService->hasPermission('portfolios.manage-media')) {
            return false;
        }

        if (!$this->authService->hasPermission('portfolios.publish')) {
            return $portfolio->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can restore the portfolio.
     */
    public function restore(CmsUserInterface $user, Portfolio $portfolio): bool
    {
        return $this->authService->hasPermission('portfolios.delete');
    }

    /**
     * Determine whether the user can permanently delete the portfolio.
     */
    public function forceDelete(CmsUserInterface $user, Portfolio $portfolio): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can view unpublished portfolios.
     */
    public function viewUnpublished(CmsUserInterface $user): bool
    {
        if (!$user || $this->authService->isAuthenticated() === false) {
            return false;
        }

        return $this->authService->hasPermission('portfolios.edit') || $this->authService->hasPermission('portfolios.publish');
    }

    /**
     * Determine whether the user can view portfolio analytics.
     */
    public function viewAnalytics(CmsUserInterface $user, Portfolio $portfolio): bool
    {
        if ($portfolio->user_id === $this->authService->getUserId()) {
            return true;
        }

        return $user->hasAnyRole(['super-admin', 'admin', 'editor']);
    }
}
