<?php
// packages/hmsoft/laravel-cms/routes/modules/content.php

use HMsoft\Cms\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

// =================================================================
// جلب الإعدادات والمتحكمات من ملف config
// =================================================================

// هذا المتغير $config يأتي من Cms.php وهو يحتوي على 'options'
$type = $config['options']['type'] ?? $module;
$bindingName = $config['options']['binding_name'] ?? "";



Route::prefix($prefix)->controller(PostController::class)->group(
    function () use ($type, $bindingName) {

        Route::get('/', 'index')->name("index")->defaults('type', $type);

        Route::post('/', 'store')->name("store")->defaults('type', $type);

        Route::get('/{' . $bindingName . '}', 'show')->name("show")->defaults('type', $type);

        Route::post('/{' . $bindingName . '}', 'update')->name("update")->defaults('type', $type);

        Route::delete('/{' . $bindingName . '}', 'destroy')->name("destroy")->defaults('type', $type);

        Route::put('/update-all', 'updateAll')->name("updateAll")->defaults('type', $type);
    }
);
