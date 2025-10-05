<?php

namespace HMsoft\Cms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use HMsoft\Cms\Contracts\AuthServiceInterface;

class CheckPermission   
{
    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request with permission checking.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$permissions  // Permission slugs to check
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        // Check if user is authenticated
        if (!$this->authService->isAuthenticated($request)) {
            return $this->unauthorizedResponse($request, 'Authentication required');
        }

        // If no permissions specified, allow access
        if (empty($permissions)) {
            return $next($request);
        }

        // Check if user has any of the required permissions
        foreach ($permissions as $permission) {
            if ($this->authService->hasPermission($permission, $request)) {
                return $next($request);
            }
        }

        // User doesn't have required permissions
        return $this->unauthorizedResponse($request, 'Insufficient permissions');
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
                    'authorization' => [__('cms::errors.403.title')]
                ]
            ], 403);
        }

        abort(403, $message);
    }
}
