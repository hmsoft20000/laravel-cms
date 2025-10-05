<?php

namespace HMsoft\Cms\Providers;

use HMsoft\Cms\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the authorization helper
        $this->app->singleton('authorization', function () {
            return new AuthServiceInterface();
        });
        // $this->app->singleton('authorization', function () {
        //     return new AuthorizationHelper();
        // });

        // Bind the interface to our concrete implementation. This is the single source of truth.
        // $this->app->singleton(AuthServiceInterface::class, AuthService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Blade directives for permission checking - DISABLED
        $this->registerBladeDirectives();
    }

    /**
     * Register custom Blade directives for authorization
     */
    private function registerBladeDirectives(): void
    {
        // @hasPermission('permission.slug')
        Blade::directive('hasPermission', function ($expression) {
            return "<?php if(app('authorization')->hasPermission{$expression}): ?>";
        });

        // @endHasPermission
        Blade::directive('endHasPermission', function () {
            return "<?php endif; ?>";
        });

        // @hasAnyPermission(['permission.1', 'permission.2'])
        Blade::directive('hasAnyPermission', function ($expression) {
            return "<?php if(app('authorization')->hasAnyPermission{$expression}): ?>";
        });

        // @endHasAnyPermission
        Blade::directive('endHasAnyPermission', function () {
            return "<?php endif; ?>";
        });

        // @hasAllPermissions(['permission.1', 'permission.2'])
        Blade::directive('hasAllPermissions', function ($expression) {
            return "<?php if(app('authorization')->hasAllPermissions{$expression}): ?>";
        });

        // @endHasAllPermissions
        Blade::directive('endHasAllPermissions', function () {
            return "<?php endif; ?>";
        });

        // @hasRole('role-slug')
        Blade::directive('hasRole', function ($expression) {
            return "<?php if(app('authorization')->hasRole{$expression}): ?>";
        });

        // @endHasRole
        Blade::directive('endHasRole', function () {
            return "<?php endif; ?>";
        });

        // @hasAnyRole(['role-1', 'role-2'])
        Blade::directive('hasAnyRole', function ($expression) {
            return "<?php if(app('authorization')->hasAnyRole{$expression}): ?>";
        });

        // @endHasAnyRole
        Blade::directive('endHasAnyRole', function () {
            return "<?php endif; ?>";
        });

        // @isAdmin
        Blade::directive('isAdmin', function () {
            return "<?php if(app('authorization')->isAdmin()): ?>";
        });

        // @endIsAdmin
        Blade::directive('endIsAdmin', function () {
            return "<?php endif; ?>";
        });

        // @isSuperAdmin
        Blade::directive('isSuperAdmin', function () {
            return "<?php if(app('authorization')->isSuperAdmin()): ?>";
        });

        // @endIsSuperAdmin
        Blade::directive('endIsSuperAdmin', function () {
            return "<?php endif; ?>";
        });

        // @can('ability', $model)
        Blade::directive('can', function ($expression) {
            return "<?php if(app('authorization')->can{$expression}): ?>";
        });

        // @endCan
        Blade::directive('endCan', function () {
            return "<?php endif; ?>";
        });
    }
}
