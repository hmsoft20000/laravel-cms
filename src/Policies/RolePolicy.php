<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;





use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Role;

use Illuminate\Auth\Access\Response;

class RolePolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any roles.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('roles.manage');
    }

    /**
     * Determine whether the user can view the role.
     */
    public function view(CmsUserInterface $user, Role $role): bool
    {
        return $this->authService->hasPermission('roles.manage');
    }

    /**
     * Determine whether the user can create roles.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('roles.manage');
    }

    /**
     * Determine whether the user can update the role.
     */
    public function update(CmsUserInterface $user, Role $role): bool
    {
        if (!$this->authService->hasPermission('roles.manage')) {
            return false;
        }

        // Prevent users from modifying roles higher than their own level
        return $role->level <= $user->roles()->max('level');
    }

    /**
     * Determine whether the user can delete the role.
     */
    public function delete(CmsUserInterface $user, Role $role): bool
    {
        if (!$this->authService->hasPermission('roles.manage')) {
            return false;
        }

        // Prevent deletion of system roles or roles with users
        if ($role->users()->count() > 0 || in_array($role->slug, ['super-admin', 'admin', 'user'])) {
            return false;
        }

        // Prevent users from deleting roles higher than their own level
        return $role->level <= $user->roles()->max('level');
    }

    /**
     * Determine whether the user can manage role hierarchy.
     */
    public function manageHierarchy(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('roles.manage');
    }

    /**
     * Determine whether the user can assign roles to users.
     */
    public function assignToUsers(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('users.manage-roles');
    }

    /**
     * Determine whether the user can manage role permissions.
     */
    public function managePermissions(CmsUserInterface $user, Role $role): bool
    {
        if (!$this->authService->hasPermission('roles.manage')) {
            return false;
        }

        // Users can only manage permissions for roles at or below their level
        return $role->level <= $user->roles()->max('level');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(CmsUserInterface $user, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(CmsUserInterface $user, Role $role): bool
    {
        return false;
    }
}
