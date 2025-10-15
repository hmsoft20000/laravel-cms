<?php

use HMsoft\Cms\Http\Controllers\Api\PermissionController;
use HMsoft\Cms\Http\Controllers\Api\RoleController;
use HMsoft\Cms\Http\Controllers\Api\UserPermissionController;
use HMsoft\Cms\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route;


// Permission Management
Route::controller(PermissionController::class)->group(function (RouteRegistrar $registrar) {
    $registrar->get('/', 'index')->name('index');
    $registrar->post('/', 'store')->name('store');
    $registrar->get('/{permission}', 'show')->name('show');
    $registrar->put('/{permission}', 'update')->name('update');
    $registrar->delete('/{permission}', 'destroy')->name('destroy');
    $registrar->get('/modules/list', 'getModules')->name('modules');
    $registrar->get('/modules/grouped', 'getByModule')->name('grouped');
    $registrar->post('/bulk-assign-role', 'bulkAssignToRole')->name('bulk-assign-role');
});

// Role Management
Route::controller(RoleController::class)->group(function (RouteRegistrar $registrar) {
    $registrar->get('/', 'index')->name('index');
    $registrar->post('/', 'store')->name('store');
    $registrar->get('/tree', 'tree')->name('tree');
    $registrar->get('/{role}', 'show')->name('show');
    $registrar->put('/{role}', 'update')->name('update');
    $registrar->delete('/{role}', 'destroy')->name('destroy');
    $registrar->post('/{role}/permissions', 'assignPermissions')->name('assign-permissions');
    $registrar->delete('/{role}/permissions', 'removePermissions')->name('remove-permissions');
    $registrar->get('/available-parents/{role?}', 'availableParents')->name('available-parents');
});

// User Permission & Role Management
Route::controller(UserPermissionController::class)->group(function (RouteRegistrar $registrar) {
    $registrar->get('/{user}/permissions', 'getUserPermissions')->name('permissions');
    $registrar->post('/{user}/permissions/assign', 'assignPermission')->name('permissions.assign');
    $registrar->delete('/{user}/permissions/revoke', 'revokePermission')->name('permissions.revoke');
    $registrar->put('/{user}/permissions/sync', 'syncPermissions')->name('permissions.sync');
    $registrar->post('/{user}/roles/assign', 'assignRole')->name('roles.assign');
    $registrar->delete('/{user}/roles/remove', 'removeRole')->name('roles.remove');
    $registrar->put('/{user}/roles/sync', 'syncRoles')->name('roles.sync');
    $registrar->get('/{user}/authorization-profile', 'getAuthorizationProfile')->name('authorization-profile');
    $registrar->post('/bulk/permissions', 'bulkAssignPermissions')->name('bulk.permissions');
    $registrar->post('/bulk/roles', 'bulkAssignRoles')->name('bulk.roles');
});
