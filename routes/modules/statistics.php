<?php

use Illuminate\Support\Facades\Route;

// Get the controller from the config
$statisticsController = cms_controller('StatisticsController');

// Define the CRUD routes.
// The prefix ('/statistics') and name prefix ('api.statistics.') will be applied from the cms.php config.
Route::get('/', [$statisticsController, 'index'])->name('index');
Route::post('/', [$statisticsController, 'store'])->name('store');
Route::put('/update-all', [$statisticsController, 'updateAll'])->name('updateAll');
Route::get('/{statistics}', [$statisticsController, 'show'])->name('show');
Route::put('/{statistics}', [$statisticsController, 'update'])->name('update');
Route::delete('/{statistics}', [$statisticsController, 'destroy'])->name('destroy');

// Route for bulk updates/reordering
// Route for image upload
Route::post('/{statistics}/image', [$statisticsController, 'updateImage'])->name('updateImage');