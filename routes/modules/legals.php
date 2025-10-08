<?php

use HMsoft\Cms\Http\Controllers\Api\LegalsController;
use Illuminate\Support\Facades\Route;


// جلب الإعدادات والمتحكمات
$type = $config['options']['type'] ?? $module;

// تعريف المسارات الأساسية
Route::controller(LegalsController::class)->group(function () use ($type) {
    Route::get('/', 'index')->name('index')->defaults('type', $type);
    Route::put('/', 'update')->name('update')->defaults('type', $type);
});

// تعريف المسارات الفرعية للوسائط (media)
// Route::prefix('{owner}/media')->name('media.')->group(function () use ($mediaController) {
//     Route::get('/', [$mediaController, 'index'])->name('index')->defaults('type', 'legal');
//     Route::post('/', [$mediaController, 'upload'])->name('upload')->defaults('type', 'legal');
//     Route::post('/reorder', [$mediaController, 'reorder'])->name('reorder')->defaults('type', 'legal');
//     Route::delete('/{medium}', [$mediaController, 'delete'])->name('delete')->defaults('type', 'legal');
// });
