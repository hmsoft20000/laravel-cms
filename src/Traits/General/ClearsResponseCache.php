<?php

namespace HMsoft\Cms\Traits\General;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

trait ClearsResponseCache
{
    protected static function bootClearsResponseCache()
    {
        static::saved(function ($model) {
            $model->flushSearchCache();
        });

        static::deleted(function ($model) {
            $model->flushSearchCache();
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->flushSearchCache();
            });
        }
    }

    /**
     * Flush cache using LIKE query on the database directly.
     * Compatible with 'database' cache driver.
     */
    public function flushSearchCache(): void
    {
        $tableName = $this->getTable();

        // 1. تحديد نمط البحث (Pattern) الخاص بنا
        $myKeyPattern = "search_results_{$tableName}_%";

        // 2. جلب إعدادات جدول الكاش
        $cacheTable = config('cache.stores.database.table', 'cache');
        $connection = config('cache.stores.database.connection');

        // 3. جلب بادئة الكاش العامة للتطبيق (Global Cache Prefix)
        // Laravel يضيف هذه البادئة تلقائياً في الداتابيس، لذا يجب أن نضمنها في الحذف
        $appCachePrefix = config('cache.prefix');

        // النمط النهائي للبحث في قاعدة البيانات
        // مثال: "laravel_cache_search_results_products_%"
        $finalPattern = $appCachePrefix . $myKeyPattern;

        // 4. تنفيذ الحذف المباشر
        try {
            DB::connection($connection)
                ->table($cacheTable)
                ->where('key', 'like', $finalPattern)
                ->delete();
        } catch (\Exception $e) {
            // في حال حدوث خطأ (مثل عدم وجود جدول الكاش)، نتجاهله لعدم تعطيل العملية
            // \Illuminate\Support\Facades\Log::warning("Failed to flush search cache: " . $e->getMessage());
        }
    }
}
