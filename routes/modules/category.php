<?php

use Illuminate\Support\Facades\Route;

// =================================================================
// get the settings and controllers from the config file
// =================================================================
$categoryController = cms_controller('CategoryController');

$type = $config['options']['type'] ?? $module;


// Category Routes
Route::controller($categoryController)->group(function () use ($type) {
    Route::get("/{$type}-categories", 'index')->name("{$type}.categories.index")->defaults('type', $type);
    Route::get("/{$type}-categories/{category}", 'show')->name("{$type}.categories.show")->defaults('type', $type);
    Route::post("/{$type}-categories", 'store')->name("{$type}.categories.store")->defaults('type', $type);
    Route::put("/{$type}-categories/{category}", 'update')->name("{$type}.categories.update")->defaults('type', $type);
    Route::delete("/{$type}-categories/{category}", 'destroy')->name("{$type}.categories.destroy")->defaults('type', $type);
});
