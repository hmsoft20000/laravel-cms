<?php

use HMsoft\Cms\Http\Controllers\Api\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::controller(StatisticsController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::put('/update-all', 'updateAll')->name('updateAll');
    Route::get('/{statistics}', 'show')->name('show');
    Route::put('/{statistics}', 'update')->name('update');
    Route::delete('/{statistics}', 'destroy')->name('destroy');
    Route::post('/{statistics}/image', 'updateImage')->name('updateImage');
});
