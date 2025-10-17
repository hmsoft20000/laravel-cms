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

        // Normal IoC binding
        App::bind($original, $extended);

        // // 🔥 Create a runtime alias (for direct instantiation)
        // if (class_exists($original) && class_exists($extended)) {
        //     // if (class_exists($original)) {
        //     //     // Attempt alias even if already loaded (safe if class isn't user-defined)
        //     //     if (!is_subclass_of($original, $extended) && $original !== $extended) {
        //     //         class_alias($extended, $original);
        //     //     }
        //     // } else {
        //     //     class_alias($extended, $original);
        //     // }

        //     // Only alias if not already aliased
        //     if (!class_exists($original, false)) {
        //         // ensure original class is loaded to avoid redeclaration errors
        //         class_alias($extended, $original);
            // }
        // }
    }

    // public function replace(string $original, string $extended): void
    // {
    //     static::$extensions[$original] = $extended;
    //     App::bind($original, $extended);
    // }

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

    public static function resolve(string $original, ...$parameters)
    {
        $class = static::getExtendedFor($original) ?? $original;
        return app()->makeWith($class, $parameters);
    }
}
