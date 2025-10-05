<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'name.required' => 'The name is required',
                'name.string' => 'The name must be a string',
                'locale.required' => 'The locale is required',
                'locale.string' => 'The locale must be a string',
                'direction.required' => 'The direction is required',
                'direction.string' => 'The direction must be a string',
                'is_active.required' => 'The active status is required',
            ],
            'attributes' => [
                'name' => 'The name',
                'locale' => 'The locale',
                'direction' => 'The direction',
                'is_active' => 'The active status',
            ],
        ],
        'update' => [
            'messages' => [
                'name.required' => 'The name is required',
                'name.string' => 'The name must be a string',
                'locale.required' => 'The locale is required',
                'locale.string' => 'The locale must be a string',
                'direction.required' => 'The direction is required',
                'direction.string' => 'The direction must be a string',
                'is_active.required' => 'The active status is required',
            ],
            'attributes' => [
                'name' => 'The name',
                'locale' => 'The locale',
                'direction' => 'The direction',
                'is_active' => 'The active status',
            ],
        ],
        'update_all' => [
            'messages' => [
                '*.required' => 'The data is required',
                '*.array' => 'The data must be an array',
                '*.id.required' => 'The language ID is required',
                '*.id.integer' => 'The language ID must be an integer',
                '*.id.exists' => 'The specified language does not exist',
                '*.name.required' => 'The name is required',
                '*.name.string' => 'The name must be a string',
                '*.locale.required' => 'The locale is required',
                '*.locale.string' => 'The locale must be a string',
                '*.direction.required' => 'The direction is required',
                '*.direction.string' => 'The direction must be a string',
                '*.is_active.required' => 'The active status is required',
            ],
            'attributes' => [
                '*' => 'The data',
                '*.id' => 'The language ID',
                '*.name' => 'The name',
                '*.locale' => 'The locale',
                '*.direction' => 'The direction',
                '*.is_active' => 'The active status',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'The specified language does not exist',
            ],
            'attributes' => [
                'id' => 'The language ID',
            ],
        ],
    ],
];
