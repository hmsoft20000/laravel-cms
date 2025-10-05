<?php

namespace HMsoft\Cms\Traits\General;

use HMsoft\Cms\Contracts\AuthServiceInterface;
use Illuminate\Http\Request;

trait HasAuthService
{
    protected AuthServiceInterface $authService;

    /**
     * Get the auth service instance
     */
    protected function getAuthService(): AuthServiceInterface
    {
        if (!isset($this->authService)) {
            $this->authService = app(AuthServiceInterface::class);
        }

        return $this->authService;
    }

    /**
     * Get the authenticated user
     */
    protected function getAuthenticatedUser(?Request $request = null)
    {
        return $this->getAuthService()->getAuthenticatedUser($request);
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated(?Request $request = null): bool
    {
        return $this->getAuthService()->isAuthenticated($request);
    }

    /**
     * Get user ID
     */
    protected function getUserId(?Request $request = null): ?int
    {
        return $this->getAuthService()->getUserId($request);
    }

    /**
     * Get user email
     */
    protected function getUserEmail(?Request $request = null): ?string
    {
        return $this->getAuthService()->getUserIdEmail($request);
    }

    /**
     * Check if user has permission
     */
    protected function hasPermission(string $permission, ?Request $request = null): bool
    {
        return $this->getAuthService()->hasPermission($permission, $request);
    }

    /**
     * Check if user has role
     */
    protected function hasRole(string $role, ?Request $request = null): bool
    {
        return $this->getAuthService()->hasRole($role, $request);
    }

    /**
     * Get user roles
     */
    protected function getUserRoles(?Request $request = null): array
    {
        return $this->getAuthService()->getUserRoles($request);
    }

    /**
     * Get user permissions
     */
    protected function getUserPermissions(?Request $request = null): array
    {
        return $this->getAuthService()->getUserPermissions($request);
    }

    /**
     * Get auth token
     */
    protected function getAuthToken(?Request $request = null): ?string
    {
        return $this->getAuthService()->getAuthToken($request);
    }

    /**
     * Validate token
     */
    protected function validateToken(string $token): bool
    {
        return $this->getAuthService()->validateToken($token);
    }
}
