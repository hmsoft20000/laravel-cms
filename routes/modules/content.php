<?php
// packages/hmsoft/laravel-cms/routes/modules/content.php

use Illuminate\Support\Facades\Route;

// =================================================================
// جلب الإعدادات والمتحكمات من ملف config
// =================================================================
$postController = cms_controller('PostController');

// هذا المتغير $config يأتي من Cms.php وهو يحتوي على 'options'
$type = $config['options']['type'] ?? $module;
$prefix = $config['options']['prefix'] ?? "";
$bindingName = $config['options']['binding_name'] ?? "";



Route::prefix($prefix)->group(
    function () use ($type, $postController, $prefix, $bindingName) {

        Route::get('/', [$postController, 'index'])
            ->name("{$prefix}.index")
            ->defaults('type', $type);

        Route::post('/', [$postController, 'store'])
            ->name("{$prefix}.store")
            ->defaults('type', $type);

        Route::get('/{' . $bindingName . '}', [$postController, 'show'])
            ->name("{$prefix}.show")
            ->defaults('type', $type);

        Route::post('/{' . $bindingName . '}', [$postController, 'update'])
            ->name("{$prefix}.update")
            ->defaults('type', $type);

        Route::delete('/{' . $bindingName . '}', [$postController, 'destroy'])
            ->name("{$prefix}.destroy")
            ->defaults('type', $type);

        Route::put('/update-all', [$postController, 'updateAll'])
            ->name("{$prefix}.updateAll")
            ->defaults('type', $type);
    }
);
