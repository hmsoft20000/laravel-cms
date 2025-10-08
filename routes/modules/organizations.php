<?php

use HMsoft\Cms\Http\Controllers\Api\OrganizationController;
use Illuminate\Support\Facades\Route;


// جلب الإعدادات والمتحكمات
$type = $config['options']['type'] ?? $module;
$prefix = $config['options']['prefix'] ?? "";
$bindingName = $config['options']['binding_name'] ?? "";

// تعريف المسارات مع إضافة الأسماء
Route::prefix($prefix)->controller(OrganizationController::class)->group(
    function () use ($type, $bindingName) {
        Route::get('/', 'index')->name("index")->defaults('type', $type);
        Route::post('/', 'store')->name("store")->defaults('type', $type);
        Route::get('/{' . $bindingName . '}', 'show')->name("show")->defaults('type', $type);
        Route::put('/{' . $bindingName . '}', 'update')->name("update")->defaults('type', $type);
        Route::delete('/{' . $bindingName . '}', 'destroy')->name("destroy")->defaults('type', $type);
        Route::post('/update-all', 'updateAll')->name("updateAll")->defaults('type', $type);
        Route::post('/{' . $bindingName . '}/image', 'updateImage')->name("updateImage")->defaults('type', $type);
    }
);
