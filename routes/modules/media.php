<?php

use Illuminate\Support\Facades\Route;


$mediaController = cms_controller('MediaController');
$legalsMediaController = cms_controller('LegalsMediaController');

// Check if we have owner_url_name (for regular content) or type (for legals)
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
    Route::controller($legalsMediaController)->group(function () {
        Route::get('/media', 'index')->name('index');
        Route::post('/media', 'store')->name('store');
        Route::get('/media/{medium}', 'show')->name('show');
        Route::put('/media/update-all', 'updateAll')->name('updateAll');
        Route::put('/media/reorder', 'reorder')->name('reorder');
        Route::put('/media/{medium}', 'update')->name('update');
        Route::delete('/media/{medium}', 'destroy')->name('destroy');
    });
}
