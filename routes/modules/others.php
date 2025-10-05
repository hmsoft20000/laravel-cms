<?php
// packages/hmsoft/laravel-cms/routes/modules/others.php

use Illuminate\Support\Facades\Route;



// sectors
Route::put('/sector/update-all', [cms_controller('SectorController'), 'updateAll'])->name('sectors.updateAll');
Route::apiResource('sector', cms_controller('SectorController'))->names('sectors');
Route::post('/sector/{sector}/image', [cms_controller('SectorController'), 'updateImage'])->name('sectors.updateImage');

// testimonials
Route::put('/testimonials/update-all', [cms_controller('TestimonialController'), 'updateAll'])->name('testimonials.updateAll');
Route::apiResource('testimonials', cms_controller('TestimonialController'))->names('testimonials');
Route::post('/testimonials/{testimonial}/image', [cms_controller('TestimonialController'), 'updateImage'])->name('testimonials.updateImage');


// contact us
Route::group(['prefix' => 'contact-us', 'as' => 'contact-us.'], function () {
    Route::post('/', [cms_controller('ContactUsController'), 'store'])->name('store');
    Route::get('/conversations', [cms_controller('ContactUsController'), 'conversations'])->name('conversations');
    Route::apiResource('/messages', cms_controller('ContactUsController'))->parameters(['messages' => 'message'])->names('messages');
    Route::post('/messages-delete-all', [cms_controller('ContactUsController'), 'destroyAll'])->name('messages.destroyAll');
    Route::post('messages/{message}/reply', [cms_controller('ContactUsController'), 'reply'])->name('messages.reply');
});

// settings
Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
    Route::get("", [cms_controller('BusinessSettingController'), "index"])->name('index');
    Route::put("", [cms_controller('BusinessSettingController'), "update"])->name('update');
    Route::get("/schema", [cms_controller('BusinessSettingController'), "schema"])->name('schema');
});

// pages-meta
Route::apiResource('pages-meta', cms_controller('PagesMetaController'))->parameters(['pages-meta' => 'pageMeta'])->names('pages-meta');
Route::put("/pages-meta", [cms_controller('PagesMetaController'), "updateAll"])->name('pages-meta.updateAll');

// languages
Route::apiResource('langs', cms_controller('LanguageController'))->names('langs');


// teams
Route::apiResource('teams', cms_controller('TeamController'))->names('teams');
Route::post('/teams/{team}/image', [cms_controller('TeamController'), 'updateImage'])->name('teams.updateImage');

// Get guest permissions
Route::get('/guest/permissions', function () {
    return successResponse(data: \HMsoft\Cms\Helpers\UserModelHelper::getGuestPermissions());
})->name('guest.permissions');
