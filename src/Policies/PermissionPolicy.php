<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;





use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Permission;


class PermissionPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any permissions.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('permissions.manage');
    }

    /**
     * Determine whether the user can view the permission.
     */
    public function view(CmsUserInterface $user, Permission $permission): bool
    {
        return $this->authService->hasPermission('permissions.manage');
    }

    /**
     * Determine whether the user can create permissions.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('permissions.manage');
    }

    /**
     * Determine whether the user can update the permission.
     */
    public function update(CmsUserInterface $user, Permission $permission): bool
    {
        return $this->authService->hasPermission('permissions.manage');
    }

    /**
     * Determine whether the user can delete the permission.
     */
    public function delete(CmsUserInterface $user, Permission $permission): bool
    {
        // Only super admins can delete permissions
        return $this->authService->hasRole('super-admin');
    }

    /**
     * Determine whether the user can manage permission assignments.
     */
    public function manageAssignments(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('permissions.manage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(CmsUserInterface $user, Permission $permission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(CmsUserInterface $user, Permission $permission): bool
    {
        return false;
    }
}
