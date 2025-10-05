<?php

namespace HMsoft\Cms\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param \Illuminate\Routing\Router $router
     * @return void
     */
    public function boot(Router $router): void
    {

        // Authorization Middleware
        $router->aliasMiddleware('check.permission', \HMsoft\Cms\Http\Middleware\CheckPermission::class);
        $router->aliasMiddleware('check.role', \HMsoft\Cms\Http\Middleware\CheckUserRole::class);
        $router->aliasMiddleware('check.role_or_permission', \HMsoft\Cms\Http\Middleware\CheckRoleOrPermission::class);
        $router->aliasMiddleware('translate.api.token.header', \HMsoft\Cms\Http\Middleware\TranslateApiTokenHeader::class);

        // Localization and Config Middleware
        $router->aliasMiddleware('set.locale', \HMsoft\Cms\Http\Middleware\SetLocale::class);
        $router->aliasMiddleware('set.locale.from.url', \HMsoft\Cms\Http\Middleware\SetLocaleFromUrl::class);
        $router->aliasMiddleware('set.language', \HMsoft\Cms\Http\Middleware\SetLanguageMiddleware::class);
        $router->aliasMiddleware('set.web_config', \HMsoft\Cms\Http\Middleware\SetWebConfigMiddleware::class);

        // // Request Handling Middleware
        $router->aliasMiddleware('detect.request_type', \HMsoft\Cms\Http\Middleware\DetectRequestType::class);
        $router->aliasMiddleware('convert.empty_strings', \HMsoft\Cms\Http\Middleware\ConvertEmptyStringsAndNullStringsToNull::class);

        // Authentication Middleware
        // Note: OptionalSanctumAuth has been moved to the main project
        $router->aliasMiddleware('custom.sanctum_stateful', \HMsoft\Cms\Http\Middleware\CustomEnsureFrontendRequestsAreStateful::class);

        // New isolated auth middleware
        $router->aliasMiddleware('cms.permission', \HMsoft\Cms\Http\Middleware\CheckPermissionMiddleware::class);
        $router->aliasMiddleware('cms.role', \HMsoft\Cms\Http\Middleware\CheckRoleMiddleware::class);
        $router->aliasMiddleware('cms.policy.auth', \HMsoft\Cms\Http\Middleware\PolicyAuthMiddleware::class);

        //        Note: auth:sanctum alias is controlled by the main application
        // to ensure proper decoupling of authentication logic
    }
}
