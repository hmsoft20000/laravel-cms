<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;





use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Statistics\Statistics;


class StatisticsPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any statistics.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Allow guest users to view statistics
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('statistics.view');
    }

    /**
     * Determine whether the user can view the statistics.
     */
    public function view(CmsUserInterface $user, Statistics $statistics): bool
    {
        // Allow guest users to view statistics
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('statistics.view');
    }

    /**
     * Determine whether the user can create statistics.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('statistics.create');
    }

    /**
     * Determine whether the user can update the statistics.
     */
    public function update(CmsUserInterface $user, Statistics $statistics): bool
    {
        return $this->authService->hasPermission('statistics.edit');
    }

    /**
     * Determine whether the user can delete the statistics.
     */
    public function delete(CmsUserInterface $user, Statistics $statistics): bool
    {
        return $this->authService->hasPermission('statistics.delete');
    }

    /**
     * Determine whether the user can update sort orders.
     */
    public function updateSortOrders(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('statistics.edit');
    }

    /**
     * Determine whether the user can restore the statistics.
     */
    public function restore(CmsUserInterface $user, Statistics $statistics): bool
    {
        return $this->authService->hasPermission('statistics.delete');
    }

    /**
     * Determine whether the user can permanently delete the statistics.
     */
    public function forceDelete(CmsUserInterface $user, Statistics $statistics): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }
}
