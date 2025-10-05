<?php

namespace HMsoft\Cms\Contracts;

use Illuminate\Http\Request;

interface AuthServiceInterface
{
    /**
     * Get the authenticated user
     *
     * @param Request|null $request
     * @return mixed|null
     */
    public function getAuthenticatedUser(?Request $request = null);

    /**
     * Check if user is authenticated
     *
     * @param Request|null $request
     * @return bool
     */
    public function isAuthenticated(?Request $request = null): bool;

    /**
     * Get user ID
     *
     * @param Request|null $request
     * @return int|null
     */
    public function getUserId(?Request $request = null): ?int;

    /**
     * Get user email
     *
     * @param Request|null $request
     * @return string|null
     */
    public function getUserIdEmail(?Request $request = null): ?string;

    /**
     * Check if user has specific permission
     *
     * @param string $permission
     * @param Request|null $request
     * @return bool
     */
    public function hasPermission(string $permission, ?Request $request = null): bool;

    /**
     * Check if user has specific role
     *
     * @param string $role
     * @param Request|null $request
     * @return bool
     */
    public function hasRole(string $role, ?Request $request = null): bool;

    /**
     * Get user roles
     *
     * @param Request|null $request
     * @return array
     */
    public function getUserRoles(?Request $request = null): array;

    /**
     * Get user permissions
     *
     * @param Request|null $request
     * @return array
     */
    public function getUserPermissions(?Request $request = null): array;

    /**
     * Get authentication token from request
     *
     * @param Request|null $request
     * @return string|null
     */
    public function getAuthToken(?Request $request = null): ?string;

    /**
     * Validate authentication token
     *
     * @param string $token
     * @return bool
     */
    public function validateToken(string $token): bool;
}
