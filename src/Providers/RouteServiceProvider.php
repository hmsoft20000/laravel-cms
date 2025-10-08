<?php

namespace HMsoft\Cms\Providers;


use HMsoft\Cms\Routing\CustomUrlGenerator;
use Illuminate\Support\Facades\Log;
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
        
        // load the API routes
        $this->loadApiRoutes();


        Route::macro('localized', function ($callback) {
            Route::group(['prefix' => '{locale?}', 'middleware' => ['set.web_config']], function () use ($callback) {
                $callback();
            });
        });


        // Default logic for 'post' models (portfolios, blogs, etc.)
        BindingService::resolver('post', function ($value) {
            $type = request()->route('type');
            $query = \HMsoft\Cms\Models\Content\Post::where('id', $value);
            if ($type) {
                $query->where('type', $type);
            }
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
            $scope = request()->route('scope');
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

        BindingService::resolver('owner', function ($value) {
            // Get the current route to determine the context
            $route = app('router')->current();

            if (!$route) {
                abort(404);
            }

            $routeName = $route->getName();
            preg_match('/^api\.([\w-]+)\./', $routeName, $matches);
            $ownerTypeName = $matches[1] ?? null;

            if (!$ownerTypeName) {
                abort(404, 'Could not determine owner type from route name.');
            }

            $modelClass = config("cms.morph_map.{$ownerTypeName}");

            if (!$modelClass || !class_exists($modelClass)) {
                abort(404, "Model for '{$ownerTypeName}' not found in morph_map.");
            }
            $modelInstance = $modelClass::where('id', $value)
                // You might want to try finding by slug as a fallback too
                // ->orWhereHas('translations', fn($q) => $q->where('slug', $value))
                ->firstOrFail();

            return $modelInstance;
        });

        // Explicit binding for medium parameter to ensure it resolves to Medium model
        BindingService::resolver('medium', function ($value) {
            return \HMsoft\Cms\Models\Shared\Medium::findOrFail($value);
        });

        // This applies all resolvers (including any developer overrides)
        // to the Laravel router.
        // BindingService::boot();
    }

    /**
     * تحميل مسارات الـ API من نظام الـ modules
     */
    private function loadApiRoutes(): void
    {
        // تحقق من أن التطبيق يعمل في وضع web أو api
        if ($this->app->runningInConsole()) {
            return;
        }

        // تحميل مسارات الـ API باستخدام نظام الـ modules
        try {

            \HMsoft\Cms\Cms::apiRoutes();
        } catch (\Exception $e) {

            // Log error for debugging
            Log::error('CMS Routes loading error: ' . $e->getMessage());
        }
    }
}
