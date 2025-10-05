<?php

use Illuminate\Support\Facades\Route;


$downloadController = cms_controller('DownloadController');
$ownerUrlName = $config['options']['owner_url_name'];


Route::controller($downloadController)->group(function () use ($ownerUrlName) {
    Route::get("/{$ownerUrlName}/{owner}/downloads", 'index')->name('index');
    Route::post("/{$ownerUrlName}/{owner}/downloads", 'store')->name('store');
    Route::get("/{$ownerUrlName}/{owner}/downloads/{download:id}", 'show')->name('show');
    Route::put("/{$ownerUrlName}/{owner}/downloads/{download:id}", 'update')->name('update');
    Route::delete("/{$ownerUrlName}/{owner}/downloads/{download:id}", 'destroy')->name('destroy');

    Route::post("/{$ownerUrlName}/{owner}/downloads/update-all", 'updateAll')->name('updateAll');
    Route::post("/{$ownerUrlName}/{owner}/downloads/{download:id}/image", 'updateImage')->name('updateImage');
});
