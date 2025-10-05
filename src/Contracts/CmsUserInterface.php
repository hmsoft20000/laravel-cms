<?php

namespace HMsoft\Cms\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface CmsUserInterface extends Authenticatable
{
    /**
     * The roles relationship.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany;

    /**
     * The direct permissions relationship.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions(): BelongsToMany;

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool;

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool;

    /**
     * Check if the user has all of the given roles.
     */
    public function hasAllRoles(array $roles): bool;

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permission): bool;

    /**
     * Check if the user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool;

    /**
     * Check if the user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool;

    /**
     * Check if the user is an administrator.
     */
    public function isAdmin(): bool;

    /**
     * Check if this is a guest user instance.
     */
    public function isGuest(): bool;

    /**
     * Get all permissions for the user (direct and via roles).
     */
    public function getAllPermissions();
}
