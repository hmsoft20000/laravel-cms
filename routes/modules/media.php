<?php

use Illuminate\Support\Facades\Route;


$mediaController = cms_controller('MediaController');
$legalsMediaController = cms_controller('LegalsMediaController');

// Check if we have owner_url_name (for regular content) or type (for legals)
$type = $config['options']['type'] ?? $module;

if (isset($config['options']['owner_url_name'])) {
    // Regular content with owner parameter
    $ownerUrlName = $config['options']['owner_url_name'];

    Route::controller($mediaController)->group(function () use ($ownerUrlName) {
        Route::get("/{$ownerUrlName}/{owner}/media", 'index')->name('index');
        Route::post("/{$ownerUrlName}/{owner}/media", 'store')->name('store');
        Route::get("/{$ownerUrlName}/{owner}/media/{medium}", 'show')->name('show');
        Route::put("/{$ownerUrlName}/{owner}/media/update-all", 'updateAll')->name('updateAll');
        Route::put("/{$ownerUrlName}/{owner}/media/reorder", 'reorder')->name('reorder');
        Route::put("/{$ownerUrlName}/{owner}/media/{medium}", 'update')->name('update');
        Route::delete("/{$ownerUrlName}/{owner}/media/{medium}", 'destroy')->name('destroy');
    });
} else {
    // Legals without owner parameter - use LegalsMediaController
    Route::controller($legalsMediaController)->group(function () use ($type) {
        Route::get('/media', 'index')->name('index')->defaults('type', $type);
        Route::post('/media', 'store')->name('store')->defaults('type', $type);
        Route::get('/media/{medium}', 'show')->name('show')->defaults('type', $type);
        Route::put('/media/update-all', 'updateAll')->name('updateAll')->defaults('type', $type);
        Route::put('/media/reorder', 'reorder')->name('reorder')->defaults('type', $type);
        Route::put('/media/{medium}', 'update')->name('update')->defaults('type', $type);
        Route::delete('/media/{medium}', 'destroy')->name('destroy')->defaults('type', $type);
    });
}
