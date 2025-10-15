<?php

namespace HMsoft\Cms\Http\Middleware;

use Closure;
use HMsoft\Cms\Models\Legal\Legal;
use Illuminate\Http\Request;

class InjectSingletonLegalOwner
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $singletonType = $route->parameter('singleton_type');

        if ($singletonType) {
            // Find the singleton Legal model by its type.
            // We use firstOrFail to ensure it exists.
            $owner = Legal::where('type', $singletonType)->firstOrFail();
            $route->setParameter('owner', $owner);
        }

        return $next($request);
    }
}
