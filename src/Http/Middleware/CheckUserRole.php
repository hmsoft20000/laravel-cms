<?php

namespace HMsoft\Cms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use HMsoft\Cms\Contracts\AuthServiceInterface;

class CheckUserRole
{
    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  // We can pass one or more roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$this->authService->isAuthenticated($request)) {
            return redirect('home');
        }

        // Check if the user has one of the required roles
        foreach ($roles as $role) {
            if ($this->authService->hasRole($role, $request)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return errorResponse(
                message: __('cms::errors.403.title'),
                state: 403
            );
        }
        abort(403, 'Unauthorized action.');
    }
}
