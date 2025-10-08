<?php

use HMsoft\Cms\Http\Controllers\Api\FaqController;
use Illuminate\Support\Facades\Route;


$ownerUrlName = $config['options']['owner_url_name'];


Route::controller(FaqController::class)->group(function () use ($ownerUrlName) {
    Route::get("/{$ownerUrlName}/{owner}/faqs", 'index')->name('index');
    Route::post("/{$ownerUrlName}/{owner}/faqs", 'store')->name('store');
    Route::get("/{$ownerUrlName}/{owner}/faqs/{faq:id}", 'show')->name('show');
    Route::put("/{$ownerUrlName}/{owner}/faqs/{faq:id}", 'update')->name('update');
    Route::delete("/{$ownerUrlName}/{owner}/faqs/{faq:id}", 'destroy')->name('destroy');

    Route::post("/{$ownerUrlName}/{owner}/faqs/update-all", 'updateAll')->name('updateAll');
});
