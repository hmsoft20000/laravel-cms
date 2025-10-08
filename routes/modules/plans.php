<?php

use HMsoft\Cms\Http\Controllers\Api\PlanController;
use Illuminate\Support\Facades\Route;

$ownerUrlName = $config['options']['owner_url_name'];


Route::controller(PlanController::class)->group(function () use ($ownerUrlName) {

    Route::get("/{$ownerUrlName}/{owner}/plans", 'index')->name("index");

    Route::post("/{$ownerUrlName}/{owner}/plans", 'store')->name("store");

    Route::get("/{$ownerUrlName}/{owner}/plans/{plan:id}", 'show')->name("show");

    Route::put("/{$ownerUrlName}/{owner}/plans/{plan:id}", 'update')->name("update");

    Route::delete("/{$ownerUrlName}/{owner}/plans/{plan:id}", 'destroy')->name("destroy");

    Route::put("/{$ownerUrlName}/{owner}/plans/update-all", 'updateAll')->name('updateAll');
});
