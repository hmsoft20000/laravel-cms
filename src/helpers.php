<?php

if (!function_exists('cms_controller')) {
    /**
     * Gets the controller class name from the package's config file.
     * This allows developers to override the default controllers.
     *
     * @param string $controllerKey The key for the controller in the 'controllers' array.
     * @return string|null
     */
    function cms_controller(string $controllerKey): ?string
    {
        $configPath = "cms.controllers.{$controllerKey}";
        $controller = config($configPath);

        // Debug: تحقق من أن الـ controller موجود
        if (!$controller) {
            throw new \Exception("Controller '{$controllerKey}' not found in config. Path: {$configPath}");
        }

        return $controller;
    }
}


if (!function_exists('cms_user_model')) {
    /**
     * يحصل على اسم كلاس موديل المستخدم من إعدادات المصادقة الأساسية للارافيل.
     * هذا يجعل المكتبة متوافقة مع أي موديل مستخدم يحدده المطور.
     *
     * @return string
     */
    function cms_user_model(): string
    {
        return \HMsoft\Cms\Helpers\UserModelHelper::getUserModelClass();
    }
}
