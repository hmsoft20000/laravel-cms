<?php

use Illuminate\Support\Facades\Route;



// جلب الإعدادات والمتحكمات
$orgController = cms_controller('OrganizationController');
$type = $config['options']['type'] ?? $module;
$prefix = $config['options']['prefix'] ?? "";
$bindingName = $config['options']['binding_name'] ?? "";

// تعريف المسارات مع إضافة الأسماء
Route::prefix($prefix)->group(
    function () use ($type, $orgController, $prefix, $bindingName) {
        Route::get('/', [$orgController, 'index'])->name("{$prefix}.index")->defaults('type', $type);
        Route::post('/', [$orgController, 'store'])->name("{$prefix}.store")->defaults('type', $type);
        Route::get('/{' . $bindingName . '}', [$orgController, 'show'])->name("{$prefix}.show")->defaults('type', $type);
        Route::put('/{' . $bindingName . '}', [$orgController, 'update'])->name("{$prefix}.update")->defaults('type', $type);
        Route::delete('/{' . $bindingName . '}', [$orgController, 'destroy'])->name("{$prefix}.destroy")->defaults('type', $type);
        Route::post('/update-all', [$orgController, 'updateAll'])->name("{$prefix}.updateAll")->defaults('type', $type);
        Route::post('/{' . $bindingName . '}/image', [$orgController, 'updateImage'])->name("{$prefix}.updateImage")->defaults('type', $type);
    }
);
