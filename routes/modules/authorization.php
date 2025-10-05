<?php
// packages/hmsoft/laravel-cms/routes/modules/authorization.php

use Illuminate\Support\Facades\Route;


// جلب المتحكمات
$permissionController = cms_controller('PermissionController');
$roleController = cms_controller('RoleController');
$userPermissionController = cms_controller('UserPermissionController');

// Permission Management
Route::group(['prefix' => 'permissions', 'as' => 'api.permissions.'], function () use ($permissionController) {
    Route::get('/', [$permissionController, 'index'])->name('index');
    Route::post('/', [$permissionController, 'store'])->name('store');
    Route::get('/{permission}', [$permissionController, 'show'])->name('show');
    Route::put('/{permission}', [$permissionController, 'update'])->name('update');
    Route::delete('/{permission}', [$permissionController, 'destroy'])->name('destroy');
    Route::get('/modules/list', [$permissionController, 'getModules'])->name('modules');
    Route::get('/modules/grouped', [$permissionController, 'getByModule'])->name('grouped');
    Route::post('/bulk-assign-role', [$permissionController, 'bulkAssignToRole'])->name('bulk-assign-role');
});

// Role Management
Route::group(['prefix' => 'roles', 'as' => 'api.roles.'], function () use ($roleController) {
    Route::get('/', [$roleController, 'index'])->name('index');
    Route::post('/', [$roleController, 'store'])->name('store');
    Route::get('/tree', [$roleController, 'tree'])->name('tree');
    Route::get('/{role}', [$roleController, 'show'])->name('show');
    Route::put('/{role}', [$roleController, 'update'])->name('update');
    Route::delete('/{role}', [$roleController, 'destroy'])->name('destroy');
    Route::post('/{role}/permissions', [$roleController, 'assignPermissions'])->name('assign-permissions');
    Route::delete('/{role}/permissions', [$roleController, 'removePermissions'])->name('remove-permissions');
    Route::get('/available-parents/{role?}', [$roleController, 'availableParents'])->name('available-parents');
});

// User Permission & Role Management
Route::group(['prefix' => 'users', 'as' => 'api.users.'], function () use ($userPermissionController) {
    Route::get('/{user}/permissions', [$userPermissionController, 'getUserPermissions'])->name('permissions');
    Route::post('/{user}/permissions/assign', [$userPermissionController, 'assignPermission'])->name('permissions.assign');
    Route::delete('/{user}/permissions/revoke', [$userPermissionController, 'revokePermission'])->name('permissions.revoke');
    Route::put('/{user}/permissions/sync', [$userPermissionController, 'syncPermissions'])->name('permissions.sync');
    Route::post('/{user}/roles/assign', [$userPermissionController, 'assignRole'])->name('roles.assign');
    Route::delete('/{user}/roles/remove', [$userPermissionController, 'removeRole'])->name('roles.remove');
    Route::put('/{user}/roles/sync', [$userPermissionController, 'syncRoles'])->name('roles.sync');
    Route::get('/{user}/authorization-profile', [$userPermissionController, 'getAuthorizationProfile'])->name('authorization-profile');
    Route::post('/bulk/permissions', [$userPermissionController, 'bulkAssignPermissions'])->name('bulk.permissions');
    Route::post('/bulk/roles', [$userPermissionController, 'bulkAssignRoles'])->name('bulk.roles');
});
