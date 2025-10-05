<?php

namespace HMsoft\Cms\Http\Middleware; // Adjust this to your application's namespace

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromUrl
{
    /**
     * Handle an incoming request.
     *
     * This middleware sets the application locale based on the 'locale' parameter
     * found in the URL. It validates the locale against a configured list of
     * supported locales and falls back to the application's default fallback locale
     * if the provided locale is invalid or not supported.
     *
     * It also sets the default locale for URL generation and stores the chosen
     * locale in the session for potential future use or persistence across requests
     * where the locale might not be in the URL (though this should be minimized
     * in a URL-driven localization strategy).
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $urlLocale = $request->route('locale');
        $supportedLocales = array_keys(config('cms.locales', []));
        $defaultLocale = config('app.fallback_locale', 'en');
        $localeToSet = null;

        if ($urlLocale && in_array($urlLocale, $supportedLocales)) {
            $localeToSet = $urlLocale;
        } else {
            $sessionLocale = Session::get('locale');
            if ($sessionLocale && in_array($sessionLocale, $supportedLocales)) {
                $localeToSet = $sessionLocale;
            } else {
                $localeToSet = $defaultLocale;
            }
            if (!$urlLocale && $localeToSet !== $defaultLocale) {
                return redirect()->to($localeToSet . '/' . $request->path());
            }
        }
        App::setLocale($localeToSet);
        URL::defaults(['locale' => $localeToSet]);
        Session::put('locale', $localeToSet);

        return $next($request);
    }
}
