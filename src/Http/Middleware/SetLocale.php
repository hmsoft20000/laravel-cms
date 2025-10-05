<?php

namespace HMsoft\Cms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{

    public function handle(Request $request, Closure $next)
    {

        $supportedLocales = array_keys(config('cms.locales', []));
        $defaultLocale = config('app.fallback_locale', 'en');

        $locale = $request->route('locale');

        // إذا ما في locale → ضيف الافتراضي
        if (!$locale) {
            return redirect()->to(
                url($defaultLocale . '/' . ltrim($request->path(), '/'))
            );
        }

        // إذا locale مو من المسموح → رجع ع الافتراضي
        if (!in_array($locale, $supportedLocales)) {
            return redirect()->to(
                url($defaultLocale . '/' . ltrim($request->path(), '/'))
            );
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
