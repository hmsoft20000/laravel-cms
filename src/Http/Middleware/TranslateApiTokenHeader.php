<?php

namespace HMsoft\Cms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TranslateApiTokenHeader
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader('X-Api-Token') && !$request->hasHeader('Authorization')) {
            $request->headers->set('Authorization', $request->header('X-Api-Token'));
        }
        return $next($request);
    }
}
