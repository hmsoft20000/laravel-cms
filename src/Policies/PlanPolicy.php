<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;





use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Shared\Plan;


class PlanPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any plans.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Allow guest users to view plans
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('plans.view');
    }

    /**
     * Determine whether the user can view the plan.
     */
    public function view(CmsUserInterface $user, Plan $plan): bool
    {
        // Allow guest users to view plans
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('plans.view');
    }

    /**
     * Determine whether the user can create plans.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('plans.create');
    }

    /**
     * Determine whether the user can update the plan.
     */
    public function update(CmsUserInterface $user, Plan $plan): bool
    {
        if (!$this->authService->hasPermission('plans.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('plans.publish')) {
            return $plan->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the plan.
     */
    public function delete(CmsUserInterface $user, Plan $plan): bool
    {
        if (!$this->authService->hasPermission('plans.delete')) {
            return false;
        }

        if (!$this->authService->hasPermission('plans.publish')) {
            return $plan->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can publish/unpublish the plan.
     */
    public function publish(CmsUserInterface $user, Plan $plan): bool
    {
        return $this->authService->hasPermission('plans.publish');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(CmsUserInterface $user, Plan $plan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(CmsUserInterface $user, Plan $plan): bool
    {
        return false;
    }
}
