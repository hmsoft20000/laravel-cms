<?php

namespace HMsoft\Cms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use HMsoft\Cms\Contracts\AuthServiceInterface;

class CheckRoleOrPermission
{
    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request with role or permission checking.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$abilities  // Roles or permissions to check (prefixed with 'role:' or 'permission:')
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$abilities)
    {
        // Check if user is authenticated
        if (!$this->authService->isAuthenticated($request)) {
            return $this->unauthorizedResponse($request, 'Authentication required');
        }

        // If no abilities specified, allow access
        if (empty($abilities)) {
            return $next($request);
        }

        // Separate roles and permissions
        $roles = [];
        $permissions = [];

        foreach ($abilities as $ability) {
            if (str_starts_with($ability, 'role:')) {
                $roles[] = str_replace('role:', '', $ability);
            } elseif (str_starts_with($ability, 'permission:')) {
                $permissions[] = str_replace('permission:', '', $ability);
            } else {
                // If not prefixed, assume it's a permission
                $permissions[] = $ability;
            }
        }

        // Check roles
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($this->authService->hasRole($role, $request)) {
                    return $next($request);
                }
            }
        }

        // Check permissions
        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
                if ($this->authService->hasPermission($permission, $request)) {
                    return $next($request);
                }
            }
        }

        // User doesn't have required roles or permissions
        return $this->unauthorizedResponse($request, 'Insufficient permissions or roles');
    }

    /**
     * Return appropriate unauthorized response
     */
    private function unauthorizedResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => [
                    'authorization' => [__('cms.errors.403.title')]
                ]
            ], 403);
        }

        abort(403, $message);
    }
}
