<?php

namespace HMsoft\Cms\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class UtilsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $utilsPath = __DIR__ . '/../Utils';
        if (File::isDirectory($utilsPath)) {
            foreach (File::files($utilsPath) as $file) {
                require_once $file->getPathname();
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
