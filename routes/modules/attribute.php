<?php

use HMsoft\Cms\Http\Controllers\Api\AttributeController;
use Illuminate\Support\Facades\Route;

$type = $config['options']['type'] ?? $module;

// Attributes Routes - مسطحة
Route::controller(AttributeController::class)->group(function () use ($type) {
    Route::get("/{$type}-attributes", 'index')->name("{$type}.attributes.index")->defaults('scope', $type);
    Route::get("/{$type}-attributes/{attribute}", 'show')->name("{$type}.attributes.show")->defaults('scope', $type);
    Route::post("/{$type}-attributes", 'store')->name("{$type}.attributes.store")->defaults('scope', $type);
    Route::put("/{$type}-attributes/{attribute}", 'update')->name("{$type}.attributes.update")->defaults('scope', $type);
    Route::post("/{$type}-attributes/updateAll", 'updateAll')->name("{$type}.attributes.updateAll")->defaults('scope', $type);
    Route::delete("/{$type}-attributes/{attribute}", 'destroy')->name("{$type}.attributes.destroy")->defaults('scope', $type);
    Route::post("/{$type}-attributes/{attribute}/image", 'updateImage')->name("{$type}.attributes.updateImage")->defaults('scope', $type);
});
