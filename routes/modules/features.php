<?php

use HMsoft\Cms\Http\Controllers\Api\FeatureController;
use Illuminate\Support\Facades\Route;


$ownerUrlName = $config['options']['owner_url_name'];


Route::controller(FeatureController::class)->group(function () use ($ownerUrlName) {
    Route::get("/{$ownerUrlName}/{owner}/features", 'index')->name('index');
    Route::post("/{$ownerUrlName}/{owner}/features", 'store')->name('store');
    Route::get("/{$ownerUrlName}/{owner}/features/{feature:id}", 'show')->name('show');
    Route::put("/{$ownerUrlName}/{owner}/features/{feature:id}", 'update')->name('update');
    Route::delete("/{$ownerUrlName}/{owner}/features/{feature:id}", 'destroy')->name('destroy');

    Route::post("/{$ownerUrlName}/{owner}/features/update-all", 'updateAll')->name('updateAll');
    Route::post("/{$ownerUrlName}/{owner}/features/{feature:id}/image", 'updateImage')->name('updateImage');
});
