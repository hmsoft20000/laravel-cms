<?php

namespace HMsoft\Cms\Http\Middleware;

use HMsoft\Cms\Models\Lang;
use HMsoft\Cms\Repositories\Contracts\BusinessSettingRepositoryInterface;
use HMsoft\Cms\Repositories\Contracts\PagesMetaRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetWebConfigMiddleware
{
    public function __construct(
        private BusinessSettingRepositoryInterface $settingsRepo,
        private PagesMetaRepositoryInterface $pagesMetaRepositoryInterface,
    ) {}
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $web_config = [];
        $language = [];

        try {

            $web_config = $this->settingsRepo->refreshCache();
            $pages_meta = $this->pagesMetaRepositoryInterface->refreshCache();

            if (isset($web_config['company_name'])) {
                Config::set('app.name');
            }

            Config::set('app.web_config', $web_config);
            Config::set('app.pages_meta', $pages_meta);

            View::share([
                'web_config' => $web_config,
                'pages_meta' => $pages_meta,
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }
        try {
            $language = Lang::active()->get();
        } catch (\Throwable $th) {
            throw $th;
        }
        View::share([
            'web_config' => $web_config,
            'pages_meta' => $pages_meta,
            'language' => $language,
            'assetsUrl' =>  storageDisk('public')->url(''),
            'defaultLang' => getDefaultLanguage()
        ]);

        return $next($request);
    }
}
