<?php

use HMsoft\Cms\Http\Controllers\Api\UserPermissionController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => UserPermissionController::class,
    'routes' => function (RouteRegistrar $registrar) {
        // We assume a prefix like 'users/{user}' is applied by the manager
        $registrar->get('/permissions', 'getUserPermissions')->name('permissions');
        $registrar->post('/permissions/assign', 'assignPermission')->name('permissions.assign');
        $registrar->delete('/permissions/revoke', 'revokePermission')->name('permissions.revoke');
        $registrar->put('/permissions/sync', 'syncPermissions')->name('permissions.sync');
        $registrar->post('/roles/assign', 'assignRole')->name('roles.assign');
        $registrar->delete('/roles/remove', 'removeRole')->name('roles.remove');
        $registrar->put('/roles/sync', 'syncRoles')->name('roles.sync');
        $registrar->get('/authorization-profile', 'getAuthorizationProfile')->name('authorization-profile');

        // These are bulk actions and probably shouldn't be nested under a single user
        // For now, we will keep them as is, assuming a different registration call.
        // Route::post('/bulk/permissions', 'bulkAssignPermissions')->name('bulk.permissions');
        // Route::post('/bulk/roles', 'bulkAssignRoles')->name('bulk.roles');
    }
];
