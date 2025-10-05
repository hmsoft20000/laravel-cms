<?php

namespace HMsoft\Cms;

use Illuminate\Support\Facades\Route;
use Closure;

class Cms
{
    /**
     * يقوم بتسجيل كل مسارات الـ API الخاصة بالحزمة بناءً على ملف الإعدادات.
     * يقبل دالة callback اختيارية للسماح للمطور بإضافة مساراته المخصصة.
     */
    public static function apiRoutes(?Closure $callback = null): void
    {

        $globalPrefix = config('cms.api_prefix', 'cms-api');
        $routeModules = config('cms.routes', []);


        if (!function_exists('cms_controller')) {
            require __DIR__ . '/helpers.php';
        }


        Route::prefix($globalPrefix)->group(function () use ($routeModules, $callback) {

            // =================================================================
            // الخطوة 1: تسجيل كل المسارات من الملفات
            // =================================================================


            foreach ($routeModules as $module => $config) {
                // تحقق إذا كانت الوحدة مفعلة في الإعدادات
                if (isset($config['enabled']) && $config['enabled'] === true) {

                    Route::group([
                        'prefix' => $config['prefix'] ?? $module,
                        'middleware' => $config['middleware'] ?? 'api',
                        'as' => $config['as'] ?? "api.{$module}.",
                    ], function () use ($config, $module) {
                        // المتغيرات $config و $module ستكون متاحة داخل ملف المسار
                        if (isset($config['file'])) {
                            $filePath = $config['file'];
                            // ======================================================
                            // المنطق الذكي الجديد هنا
                            // ======================================================
                            // التحقق مما إذا كان المسار المقدم ليس مسارًا مطلقًا
                            // المسار المطلق يبدأ بـ "/" أو "C:\" (للـ Windows)
                            if (!str_starts_with($filePath, '/') && !preg_match('/^[a-zA-Z]:\\\\/', $filePath)) {
                                // إذا لم يكن مطلقًا، قم ببناء المسار الافتراضي من داخل الحزمة
                                $filePath = __DIR__ . '/../routes/modules/' . $filePath;
                            }

                            if (file_exists($filePath)) {
                                require $filePath;
                            }
                        }
                    });
                }
            }

            // =================================================================
            // الخطوة 2: تطبيق التجاوزات (Overrides) من ملف الإعدادات
            // =================================================================
            self::applyRouteOverrides();

            // =================================================================
            // الخطوة 3: تنفيذ أي مسارات مخصصة من المطور
            // =================================================================
            if ($callback) {
                $callback();
            }
        });
    }

    /**
     * يقرأ مصفوفة 'overrides' من الإعدادات ويطبقها على المسارات المسجلة.
     * هذا هو الجزء الذي يمنح المطورين التحكم الدقيق.
     */
    private static function applyRouteOverrides(): void
    {
        $overrides = config('cms.overrides', []);
        $routes = Route::getRoutes();

        foreach ($overrides as $routeName => $config) {
            /** @var \Illuminate\Routing\Route|null $route */
            $route = $routes->getByName($routeName);

            // تجاهل إذا كان اسم المسار غير موجود
            if (!$route) {
                continue;
            }

            // الخيار 1: تعطيل المسار
            if (isset($config['enabled']) && $config['enabled'] === false) {
                // الطريقة الأسهل لـ "تعطيل" مسار هي جعله يرجع خطأ 404
                $route->setAction(['uses' => function () {
                    abort(404);
                }]);
                continue; // انتقل للمسار التالي
            }

            // الخيار 2: تغيير الـ URI
            if (isset($config['uri'])) {
                // نأخذ البادئة من المسار الأصلي ونضيف الـ URI الجديد
                $route->setUri(trim($route->getPrefix(), '/') . '/' . trim($config['uri'], '/'));
            }

            // الخيار 3: استبدال أو إضافة Middleware
            if (isset($config['middleware'])) {
                // setMiddleware سيقوم باستبدال كل الـ middleware القديم
                $route->middleware($config['middleware']);
            }

            // الخيار 4: تغيير الـ Controller@method (الفعل)
            if (isset($config['action'])) {
                $route->uses($config['action']);
            }
        }
    }
}
