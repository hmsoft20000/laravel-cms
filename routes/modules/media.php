<?php

use HMsoft\Cms\Http\Controllers\Api\MediaController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    /**
     * Controller for standard polymorphic media resources.
     */
    'controller' => MediaController::class,

    /**
     * Routes for media attached to a parent model (e.g., a portfolio or blog).
     * The prefix (e.g., 'portfolios/{owner}/media') is applied by the CmsRouteManager.
     */
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->post('/bulk-upload-files', 'bulkUpload')->name('bulk-upload-files');
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->get('/{medium}', 'show')->name('show');
        $registrar->put('/reorder', 'reorder')->name('reorder');
        $registrar->put('/{medium}', 'update')->name('update');
        $registrar->delete('/{medium}', 'destroy')->name('destroy');
    }
];
