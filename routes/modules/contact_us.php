<?php

use HMsoft\Cms\Http\Controllers\Api\ContactUsController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => ContactUsController::class,
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->post('/', 'store')->name('store');
        $registrar->get('/conversations', 'conversations')->name('conversations');
        $registrar->post('/messages-delete-all', 'destroyAll')->name('messages.destroyAll');
        $registrar->post('messages/{message}/reply', 'reply')->name('messages.reply');
        $registrar->get('/messages', 'index')->name('messages.index');
        $registrar->put('/messages/{message}', 'update')->name('messages.update')->parameters(['messages' => 'message']);
        // $registrar->apiResource('/messages', 'messages')->parameters(['messages' => 'message']);
    }
];
