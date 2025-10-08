<?php

use HMsoft\Cms\Http\Controllers\Api\PermissionController;
use HMsoft\Cms\Http\Controllers\Api\RoleController;
use HMsoft\Cms\Http\Controllers\Api\UserPermissionController;
use Illuminate\Support\Facades\Route;


// Permission Management
Route::controller(PermissionController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/{permission}', 'show')->name('show');
    Route::put('/{permission}', 'update')->name('update');
    Route::delete('/{permission}', 'destroy')->name('destroy');
    Route::get('/modules/list', 'getModules')->name('modules');
    Route::get('/modules/grouped', 'getByModule')->name('grouped');
    Route::post('/bulk-assign-role', 'bulkAssignToRole')->name('bulk-assign-role');
});

// Role Management
Route::controller(RoleController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/tree', 'tree')->name('tree');
    Route::get('/{role}', 'show')->name('show');
    Route::put('/{role}', 'update')->name('update');
    Route::delete('/{role}', 'destroy')->name('destroy');
    Route::post('/{role}/permissions', 'assignPermissions')->name('assign-permissions');
    Route::delete('/{role}/permissions', 'removePermissions')->name('remove-permissions');
    Route::get('/available-parents/{role?}', 'availableParents')->name('available-parents');
});

// User Permission & Role Management
Route::controller(UserPermissionController::class)->group(function () {
    Route::get('/{user}/permissions', 'getUserPermissions')->name('permissions');
    Route::post('/{user}/permissions/assign', 'assignPermission')->name('permissions.assign');
    Route::delete('/{user}/permissions/revoke', 'revokePermission')->name('permissions.revoke');
    Route::put('/{user}/permissions/sync', 'syncPermissions')->name('permissions.sync');
    Route::post('/{user}/roles/assign', 'assignRole')->name('roles.assign');
    Route::delete('/{user}/roles/remove', 'removeRole')->name('roles.remove');
    Route::put('/{user}/roles/sync', 'syncRoles')->name('roles.sync');
    Route::get('/{user}/authorization-profile', 'getAuthorizationProfile')->name('authorization-profile');
    Route::post('/bulk/permissions', 'bulkAssignPermissions')->name('bulk.permissions');
    Route::post('/bulk/roles', 'bulkAssignRoles')->name('bulk.roles');
});
