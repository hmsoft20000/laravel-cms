<?php

return [
    'validation' => [
        'update_all' => [
            'messages' => [
                '*.required' => 'The data is required.',
                '*.array' => 'The data must be an array.',
                '*.id.required' => 'The permission ID is required.',
                '*.id.integer' => 'The permission ID must be an integer.',
                '*.id.exists' => 'The selected permission does not exist.',
                '*.name.required' => 'The permission name is required.',
                '*.name.string' => 'The permission name must be a string.',
                '*.name.max' => 'The permission name must be less than 255 characters.',
                '*.slug.required' => 'The permission slug is required.',
                '*.slug.string' => 'The permission slug must be a string.',
                '*.slug.max' => 'The permission slug must be less than 255 characters.',
                '*.description.string' => 'The permission description must be a string.',
                '*.module.required' => 'The permission module is required.',
                '*.module.string' => 'The permission module must be a string.',
                '*.module.max' => 'The permission module must be less than 255 characters.',
            ],
            'attributes' => [
                '*' => 'Data',
                '*.id' => 'Permission ID',
                '*.name' => 'Permission Name',
                '*.slug' => 'Permission Slug',
                '*.description' => 'Permission Description',
                '*.module' => 'Permission Module',
            ],
        ],
    ],
];
