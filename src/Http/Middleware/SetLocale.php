<?php

namespace HMsoft\Cms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request to set the application's locale.
     *
     * This middleware determines the best locale for the user based on a
     * priority system:
     * 1. Check for a `locale` parameter in the URL.
     * 2. If not in URL, parse the `Accept-Language` HTTP header.
     * 3. If not in header, check for a `locale` value in the session.
     * 4. If none of the above, use the application's fallback locale.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /* get supported locales */
        $supportedLocales = array_keys(config('cms.locales', []));

        /* get fallback locale */
        $fallbackLocale = config('app.fallback_locale', 'en');

        /* determine locale */
        $locale = $this->determineLocale($request, $supportedLocales, $fallbackLocale);

        /* set locale */
        App::setLocale($locale);

        /* set session locale */
        Session::put('locale', $locale);
        // Optional: If you want to automatically add the locale to generated URLs.
        // \Illuminate\Support\Facades\URL::defaults(['locale' => $locale]);
        return $next($request);
    }

    /**
     * Determine the locale from the request based on priority.
     */
    protected function determineLocale(Request $request, array $supportedLocales, string $fallbackLocale): string
    {
        /* check url segment */
        if ($request->route('locale') && in_array($request->route('locale'), $supportedLocales)) {
            return $request->route('locale');
        }

        /* check accept language header */
        $acceptLang = $request->header('Accept-Language');
        if ($acceptLang) {
            foreach (explode(',', $acceptLang) as $lang) {
                $localeCode = strtolower(trim(explode(';', $lang)[0]));
                if (in_array($localeCode, $supportedLocales)) {
                    return $localeCode;
                }
            }
        }

        /* check session */
        if (Session::has('locale') && in_array(Session::get('locale'), $supportedLocales)) {
            return Session::get('locale');
        }

        /* return fallback */
        return $fallbackLocale;
    }
}
