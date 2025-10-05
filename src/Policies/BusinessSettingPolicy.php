<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;



use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\BusinessSetting;
use HMsoft\Cms\Traits\General\HasPolicyAuth;
use HMsoft\Cms\Helpers\PolicyHelper;

class BusinessSettingPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    use HasPolicyAuth;
    /**
     * Determine whether the user can view any business settings.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Use PolicyHelper to get the authenticated user
        $currentUser = PolicyHelper::getAuthenticatedUser();
        if (!$currentUser) {
            return false;
        }

        // For testing: allow admin users to view settings
        if (PolicyHelper::hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }

        return PolicyHelper::hasPermission('settings.view');
    }

    /**
     * Determine whether the user can view the business setting.
     */
    public function view(CmsUserInterface $user, BusinessSetting $businessSetting): bool
    {
        // Use PolicyHelper to get the authenticated user
        $currentUser = PolicyHelper::getAuthenticatedUser();
        if (!$currentUser) {
            return false;
        }

        // For testing: allow admin users to view settings
        if (PolicyHelper::hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }

        return PolicyHelper::hasPermission('settings.view');
    }

    /**
     * Determine whether the user can create business settings.
     */
    public function create(CmsUserInterface $user): bool
    {
        // Use the new auth system
        $currentUser = $this->getPolicyUser();
        if (!$currentUser) {
            return false;
        }

        return $this->policyUserHasPermission('settings.edit');
    }

    /**
     * Determine whether the user can update the business setting.
     */
    public function update(CmsUserInterface $user, BusinessSetting $businessSetting): bool
    {
        // Use the new auth system
        $currentUser = $this->getPolicyUser();
        if (!$currentUser) {
            return false;
        }

        return $this->policyUserHasPermission('settings.edit');
    }

    /**
     * Determine whether the user can delete the business setting.
     */
    public function delete(CmsUserInterface $user, BusinessSetting $businessSetting): bool
    {
        // Use the new auth system
        $currentUser = $this->getPolicyUser();
        if (!$currentUser) {
            return false;
        }

        // Only super admins can delete business settings
        return $this->policyUserHasRole('super-admin');
    }

    /**
     * Determine whether the user can manage system settings.
     */
    public function manage(CmsUserInterface $user): bool
    {
        // Use the new auth system
        $currentUser = $this->getPolicyUser();
        if (!$currentUser) {
            return false;
        }

        return $this->policyUserHasPermission('settings.edit');
    }

    /**
     * Determine whether the user can manage images for business settings.
     */
    public function manageImages(CmsUserInterface $user): bool
    {
        // Use the new auth system
        $currentUser = $this->getPolicyUser();
        if (!$currentUser) {
            return false;
        }

        return $this->policyUserHasPermission('settings.edit');
    }

    /**
     * Determine whether the user can export business settings.
     */
    public function export(CmsUserInterface $user): bool
    {
        // Use the new auth system
        $currentUser = $this->getPolicyUser();
        if (!$currentUser) {
            return false;
        }

        return $this->policyUserHasPermission('settings.view');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(CmsUserInterface $user, BusinessSetting $businessSetting): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(CmsUserInterface $user, BusinessSetting $businessSetting): bool
    {
        return false;
    }
}
