<?php

use HMsoft\Cms\Http\Controllers\Api\DownloadItemController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    /**
     * Controller for this module's routes.
     */
    'controller' => DownloadItemController::class,

    /**
     * Routes for this module.
     * The prefix (e.g., 'portfolios/{owner}/downloads') is applied by the CmsRouteManager.
     */
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->get('/{downloadItem}', 'show')->name('show');
        $registrar->put('/{downloadItem}', 'update')->name('update');
        $registrar->delete('/{downloadItem}', 'destroy')->name('destroy');
        $registrar->post('/{downloadItem}/image', 'updateImage')->name('updateImage');
    }
];
