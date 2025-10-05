<?php

namespace HMsoft\Cms\Providers;


use HMsoft\Cms\Routing\CustomUrlGenerator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
            Route::group(['prefix' => '{locale?}', 'middleware' => ['set.web_config', 'set.locale']], function () use ($callback) {
                $callback();
            });
        });

        Route::bind('post', function ($value) {
            // get the 'type' from the current route (e.g., 'portfolio', 'blog')
            $type = request()->route('type');

            $query = \HMsoft\Cms\Models\Content\Post::where('id', $value);

            // make sure the element is of the correct type as defined in the path
            if ($type) {
                $query->where('type', $type);
            }

            return $query->firstOrFail();
        });

        Route::bind('category', function ($value) {
            $type = request()->route('type');

            $category = \HMsoft\Cms\Models\Shared\Category::where('id', $value)
                ->where('type', $type)
                ->firstOrFail();

            return $category;
        });

        Route::bind('attribute', function ($value) {
            // get the 'scope' from the current route (e.g., 'portfolio', 'product')
            $scope = request()->route('scope');

            // find the attribute by the ID and make sure it is from the correct scope
            $attribute = \HMsoft\Cms\Models\Shared\Attribute::where('id', $value)
                ->where('scope', $scope)
                ->firstOrFail();

            return $attribute;
        });

        Route::bind('sponsor', function ($value) {

            $query = \HMsoft\Cms\Models\Organizations\Organization::query();
            $query->where('id', $value)->where('type', 'sponsor');
            return $query->firstOrFail();
        });

        Route::bind('partner', function ($value) {

            $query = \HMsoft\Cms\Models\Organizations\Organization::query();
            $query->where('id', $value)->where('type', 'partner');
            return $query->firstOrFail();
        });

        Route::bind('owner', function ($value) {
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
        Route::bind('medium', function ($value) {
            return \HMsoft\Cms\Models\Shared\Medium::findOrFail($value);
        });
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
