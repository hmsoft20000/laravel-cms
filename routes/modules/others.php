<?php
// packages/hmsoft/laravel-cms/routes/modules/others.php

use HMsoft\Cms\Http\Controllers\Api\{
    BusinessSettingController,
    ContactUsController,
    LanguageController,
    PagesMetaController,
    SectorController,
    TeamController,
    TestimonialController,
};
use Illuminate\Support\Facades\Route;

// sectors
Route::controller(SectorController::class)->group(function () {
    Route::put('/sector/update-all', 'updateAll')->name('sectors.updateAll');
    Route::apiResource('sector', 'sector')->names('sectors');
    Route::post('/sector/{sector}/image', 'updateImage')->name('sectors.updateImage');
});

// testimonials
Route::controller(TestimonialController::class)->group(function () {
    Route::put('/testimonials/update-all', 'updateAll')->name('testimonials.updateAll');
    Route::apiResource('testimonials', 'testimonials')->names('testimonials');
    Route::post('/testimonials/{testimonial}/image', 'updateImage')->name('testimonials.updateImage');
});


// contact us
Route::controller(ContactUsController::class)->group(['prefix' => 'contact-us', 'as' => 'contact-us.'], function () {
    Route::post('/', 'store')->name('store');
    Route::get('/conversations', 'conversations')->name('conversations');
    Route::apiResource('/messages', 'messages')->parameters(['messages' => 'message'])->names('messages');
    Route::post('/messages-delete-all', 'destroyAll')->name('messages.destroyAll');
    Route::post('messages/{message}/reply', 'reply')->name('messages.reply');
});

// settings
Route::controller(BusinessSettingController::class)->group(['prefix' => 'settings', 'as' => 'settings.'], function () {
    Route::get("", "index")->name('index');
    Route::put("", "update")->name('update');
    Route::get("/schema", "schema")->name('schema');
});

// pages-meta

Route::controller(PagesMetaController::class)->group(function () {
    Route::apiResource('pages-meta', 'pages-meta')->parameters(['pages-meta' => 'pageMeta'])->names('pages-meta');
    Route::put("/pages-meta", "updateAll")->name('pages-meta.updateAll');
});

// languages
Route::controller(LanguageController::class)->group(function () {
    Route::apiResource('langs', 'langs')->names('langs');
});


// teams
Route::controller(TeamController::class)->group(function () {
    Route::apiResource('teams', 'teams')->names('teams');
    Route::post('/teams/{team}/image', 'updateImage')->name('teams.updateImage');
});

// Get guest permissions
Route::get('/guest/permissions', function () {
    return successResponse(data: \HMsoft\Cms\Helpers\UserModelHelper::getGuestPermissions());
})->name('guest.permissions');
