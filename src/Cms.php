<?php

namespace HMsoft\Cms;

use Illuminate\Support\Facades\App;
use Closure;

/**
 * The core class for managing CMS extensions and configurations.
 *
 * الكلاس الأساسي لإدارة توسعات وإعدادات نظام إدارة المحتوى.
 */
class Cms
{
    /**
     * Stores all registered extension mappings.
     * @var array
     */
    protected static array $extensions = [];

    /**
     * The main method for developers to register their extensions.
     * الدالة الرئيسية للمطورين لتسجيل توسعاتهم.
     *
     * @param Closure $callback A closure that receives this Cms instance.
     */
    public static function extend(Closure $callback): void
    {
        $callback(app(static::class));
    }

    /**
     * Replaces a core package class with a custom class.
     * استبدال كلاس أساسي من الحزمة بكلاس مخصص.
     *
     * @param string $original The original class from the package.
     * @param string $extended The developer's custom class.
     */
    public function replace(string $original, string $extended): void
    {
        static::$extensions[$original] = $extended;
        App::bind($original, $extended);
    }

    /**
     * Get the extended class for a given original class, if it exists.
     * جلب الكلاس الموسع لكلاس أصلي معين، إذا كان موجودًا.
     *
     * @param string $original
     * @return string|null
     */
    public static function getExtendedFor(string $original): ?string
    {
        return static::$extensions[$original] ?? null;
    }
}
