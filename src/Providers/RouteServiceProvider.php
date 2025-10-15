<?php

namespace HMsoft\Cms\Providers;


use HMsoft\Cms\Routing\CustomUrlGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use HMsoft\Cms\Services\BindingService;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->extend('url', function ($service, $app) {
            return new CustomUrlGenerator(
                $app['router']->getRoutes(),
                $app->rebinding(
                    'request',
                    function ($app, $request) use (&$url) {
                        $url->setRequest($request);
                    }
                ),
                $app['config']['app.asset_url']
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Route::macro('localized', function ($callback) {
            Route::group(['prefix' => '{locale?}', 'middleware' => ['set.web_config']], function () use ($callback) {
                $callback();
            });
        });


        // Default logic for 'portfolio' models (portfolios, blogs, etc.)
        BindingService::resolver('portfolio', function ($value) {
            $query = \HMsoft\Cms\Models\Content\Portfolio::where('id', $value);
            return $query->firstOrFail();
        });

        // Default logic for 'blog' models (portfolios, blogs, etc.)
        BindingService::resolver('blog', function ($value) {
            $query = \HMsoft\Cms\Models\Content\Blog::where('id', $value);
            return $query->firstOrFail();
        });

        // Default logic for 'service' models (portfolios, blogs, etc.)
        BindingService::resolver('service', function ($value) {
            $query = \HMsoft\Cms\Models\Content\Service::where('id', $value);
            return $query->firstOrFail();
        });

        // Default logic for 'category' models
        BindingService::resolver('category', function ($value) {
            $type = request()->route('type');
            return \HMsoft\Cms\Models\Shared\Category::where('id', $value)
                ->where('type', $type)
                ->firstOrFail();
        });


        // Default logic for 'attribute' models
        BindingService::resolver('attribute', function ($value) {
            $scope = request()->route('type');
            return \HMsoft\Cms\Models\Shared\Attribute::where('id', $value)
                ->where('scope', $scope)
                ->firstOrFail();
        });


        BindingService::resolver('sponsor', function ($value) {

            $query = \HMsoft\Cms\Models\Organizations\Organization::query();
            $query->where('id', $value)->where('type', 'sponsor');
            return $query->firstOrFail();
        });

        BindingService::resolver('partner', function ($value) {

            $query = \HMsoft\Cms\Models\Organizations\Organization::query();
            $query->where('id', $value)->where('type', 'partner');
            return $query->firstOrFail();
        });

        BindingService::resolver('feature', function ($value) {
            return \HMsoft\Cms\Models\Shared\Feature::findOrFail($value);
        });

        // BindingService::resolver('owner', function ($value) {
        //     // Get the current route to determine the context
        //     $route = app('router')->current();

        //     if (!$route) {
        //         abort(404);
        //     }

        //     $routeName = $route->getName();
        //     preg_match('/^api\.([\w-]+)\./', $routeName, $matches);
        //     $ownerTypeName = $matches[1] ?? null;

        //     if (!$ownerTypeName) {
        //         abort(404, 'Could not determine owner type from route name.');
        //     }

        //     $modelClass = config("cms.morph_map.{$ownerTypeName}");

        //     if (!$modelClass || !class_exists($modelClass)) {
        //         abort(404, "Model for '{$ownerTypeName}' not found in morph_map.");
        //     }
        //     $modelInstance = $modelClass::where('id', $value)
        //         // You might want to try finding by slug as a fallback too
        //         // ->orWhereHas('translations', fn($q) => $q->where('slug', $value))
        //         ->firstOrFail();

        //     return $modelInstance;
        // });


        BindingService::resolver('legal_type', function ($value) {
            // $value will be the string from the URL, e.g., "aboutUs"
            return \HMsoft\Cms\Models\Legal\Legal::where('type', $value)->firstOrFail();
        });

        BindingService::resolver('owner', function ($value, \Illuminate\Routing\Route $route) {
            // get the owner binding key from the route
            $ownerKey = $route->parameter('_owner_binding_key');
            if (!$ownerKey) {
                // This happens if ownerBinding() was not used, fallback to default behavior or abort
                // return null; // or abort
                abort(404);
            }


            // get the owner model from the route
            $ownerModel = $route->parameter($ownerKey);

            info([
                'resolver ownerModel' => $ownerModel,
                'resolver ownerKey' => $ownerKey,
                'resolver rsoute' => $route
            ]);
            return $ownerModel;
        });

        // Explicit binding for medium parameter to ensure it resolves to Medium model
        BindingService::resolver('medium', function ($value) {
            return \HMsoft\Cms\Models\Shared\Medium::findOrFail($value);
        });

        // This applies all resolvers (including any developer overrides)
        // to the Laravel router.
        // BindingService::boot();
    }
}
