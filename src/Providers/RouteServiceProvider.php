<?php

namespace HMsoft\Cms\Providers;

use HMsoft\Cms\Models\Lang;
use HMsoft\Cms\Routing\CustomUrlGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use HMsoft\Cms\Services\BindingService;
use Illuminate\Support\Facades\Cache;

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

        // Route::macro('localized', function ($callback) {
        //     Route::group(['prefix' => '{locale?}', 'middleware' => ['set.web_config']], function () use ($callback) {
        //         $callback();
        //     });
        // });

        // Route::macro('localized', function ($callback) {
        //     // 1. جلب اللغات المدعومة من الإعدادات وتحويلها لنص مفصول بـ | (مثل: ar|en)
        //     $supportedLocales = implode('|', array_keys(config('app.locales')));

        //     // في حال لم تكن الإعدادات محملة، نضع قيمة افتراضية لتجنب الأخطاء
        //     if (empty($supportedLocales)) $supportedLocales = 'ar|en';

        //     // 2. تعريف المجموعة مع شرط (where)
        //     Route::prefix('{locale?}')
        //         ->middleware(['set.web_config'])
        //         ->where(['locale' => $supportedLocales]) // <--- هذا السطر هو الحل السحري
        //         ->group($callback);
        // });


        Route::macro('localized', function ($callback) {

            // 1. جلب رموز اللغات النشطة بذكاء (من الكاش أو الداتابيز)
            $supportedLocales = Cache::rememberForever('cms_active_locales_codes', function () {
                try {
                    // نجلب فقط اللغات المفعلة (أو حسب المنطق الخاص بك في الـ CMS)
                    // إذا لم يكن لديك دالة active() استخدم where('is_active', 1)
                    return Lang::active()->pluck('locale')->toArray();
                } catch (\Throwable $e) {
                    // هذه الـ Catch ضرورية جداً لحماية النظام أثناء تشغيل php artisan migrate 
                    // لأول مرة عندما لا يكون جدول اللغات موجوداً في قاعدة البيانات بعد
                    return [];
                }
            });

            // $supportedLocales = Lang::active()->pluck('locale')->toArray();

            // 2. إذا فشل الجلب (أو كان الجدول فارغاً)، نعتمد على لغة النظام الافتراضية
            if (empty($supportedLocales)) {
                $supportedLocales = [config('app.fallback_locale', 'en')];
            }

            // 3. التحقق مما إذا كان الموقع متعدد اللغات (أكثر من لغة واحدة مفعلة)
            $isMultilingual = count($supportedLocales) > 1;


            // 4. السلوك الأول: الموقع ذو لغة واحدة (لا نستخدم أي Prefix)
            if (!$isMultilingual) {
                Route::middleware(['set.web_config'])->group($callback);
                return; // نخرج من الـ Macro هنا
            }

            // 5. السلوك الثاني: الموقع متعدد اللغات (نطبق نظام اللاحقة)
            $localeRegex = implode('|', $supportedLocales);


            Route::prefix('{locale?}')
                ->middleware(['set.web_config'])
                ->where(['locale' => $localeRegex])
                ->group($callback);



            // // 1. جلب اللغات المدعومة من الإعدادات وتحويلها لنص مفصول بـ | (مثل: ar|en)
            // $supportedLocales = implode('|', array_keys(config('cms.locales', [])));

            // // في حال لم تكن الإعدادات محملة، نضع قيمة افتراضية لتجنب الأخطاء
            // if (empty($supportedLocales)) $supportedLocales = 'ar|en';

            // // 2. تعريف المجموعة مع شرط (where)
            // Route::prefix('{locale?}')
            //     ->middleware(['set.web_config'])
            //     ->where(['locale' => $supportedLocales]) // <--- هذا السطر هو الحل السحري
            //     ->group($callback);
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
