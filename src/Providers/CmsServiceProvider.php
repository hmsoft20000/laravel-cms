<?php

namespace HMsoft\Cms\Providers;

use HMsoft\Cms\Console\Commands\CmsInstallCommand;
use HMsoft\Cms\Routing\CmsRouteManager;
use HMsoft\Cms\Services\BindingService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class CmsServiceProvider extends ServiceProvider
{

    /**
     * The providers to register.
     *
     * @var array
     */
    protected $providers = [
        RepositoryServiceProvider::class,
        // AuthServiceProvider::class, // DISABLED: Authorization logic commented out
        // AuthorizationServiceProvider::class, // DISABLED: Authorization logic commented out
        RouteServiceProvider::class,
        MiddlewareServiceProvider::class,
        UtilsServiceProvider::class,
    ];


    /**
     * Register any application services.
     */
    public function register(): void
    {
        try {
            if (PUBLIC_PATH_OVERRIDE) {
                app()->usePublicPath(PUBLIC_PATH_OVERRIDE);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        // دمج ملف الإعدادات
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/cms.php',
            'cms'
        );

        // دمج ملف schema الإعدادات
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/cms_settings_schema.php',
            'cms_settings_schema'
        );

        // دمج ملف ثوابت الإعدادات
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/cms_constants.php',
            'cms_constants'
        );

        $this->app->singleton(Cms::class, function () {
            return new Cms();
        });

        $this->app->singleton('cms.route-manager', function () {
            return new CmsRouteManager();
        });

        // يمكنك أيضًا تسجيل الـ Facade هنا إذا لم تكن تستخدم auto-discovery
        $this->app->alias('CmsRoute', \HMsoft\Cms\Facades\CmsRoute::class);


        $this->app->singleton('cms.binding-service', BindingService::class);
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('BindingService', \HMsoft\Cms\Services\BindingService::class);

        // تسجيل كل الـ Providers المتخصصة
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }

        // تسجيل أوامر Artisan الخاصة بالحزمة
        if ($this->app->runningInConsole()) {
            $this->commands([
                CmsInstallCommand::class,
                // يمكنك إضافة أي أوامر أخرى هنا في المستقبل
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // === تحميل مكونات الحزمة ===
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'cms');
        $this->publishes([
            // The source file in your package
            __DIR__ . '/../../config/cms.php' => config_path('cms.php'),
            __DIR__ . '/../../config/cms_constants.php' => config_path('cms_constants.php'),
        ], 'cms-config'); // <-- The tag must match exactly

        $this->publishes([
            __DIR__ . '/../../resources/lang' => $this->app->langPath('vendor/cms'),
        ], 'cms-lang');

        // === كود منقول من AppServiceProvider ===
        if (!$this->app->runningInConsole()) {
            $host = request()->getHost();
            $scheme = request()->getScheme();
            $port = request()->getPort();
            $basePath = parse_url(env('APP_URL'), PHP_URL_PATH);
            $dynamicAppUrl = "$scheme://$host";
            if (!in_array($port, [80, 443])) {
                $dynamicAppUrl .= ":$port";
            }
            $dynamicAppUrl .= $basePath;

            config([
                'app.url' => $dynamicAppUrl,
                'reverb.apps.apps.0.options.host' => $host,
                'reverb.apps.apps.0.options.port' => $port,
                'reverb.apps.apps.0.options.scheme' => $scheme,
                'reverb.apps.apps.0.options.useTLS' => $scheme === 'https',
            ]);
        }

        // Load the Morph Map from our config file
        // This allows end-developers to define their own models in the config
        Relation::morphMap(config('cms.morph_map', []));
    }
}
