<?php
// packages/hmsoft/laravel-cms/routes/modules/legals.php

use Illuminate\Support\Facades\Route;


// جلب الإعدادات والمتحكمات
$legalsController = cms_controller('LegalsController');
$mediaController = cms_controller('MediaController');
$type = $config['options']['type'] ?? $module;

// تعريف المسارات الأساسية
Route::get('/', [$legalsController, 'index'])->name('index')->defaults('type', $type);
Route::put('/', [$legalsController, 'update'])->name('update')->defaults('type', $type);

// تعريف المسارات الفرعية للوسائط (media)
// Route::prefix('{owner}/media')->name('media.')->group(function () use ($mediaController) {
//     Route::get('/', [$mediaController, 'index'])->name('index')->defaults('type', 'legal');
//     Route::post('/', [$mediaController, 'upload'])->name('upload')->defaults('type', 'legal');
//     Route::post('/reorder', [$mediaController, 'reorder'])->name('reorder')->defaults('type', 'legal');
//     Route::delete('/{medium}', [$mediaController, 'delete'])->name('delete')->defaults('type', 'legal');
// });
