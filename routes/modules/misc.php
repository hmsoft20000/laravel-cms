<?php
// packages/hmsoft/laravel-cms/routes/modules/others.php

use Illuminate\Support\Facades\Route;

// Get guest permissions
Route::get('/guest/permissions', function () {
    return successResponse(data: \HMsoft\Cms\Helpers\UserModelHelper::getGuestPermissions());
})->name('guest.permissions');
