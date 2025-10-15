<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'file.file' => 'The file must be a file.',
                'file.max' => 'The file may not be greater than 10240 kilobytes.',
                'locales.required' => 'The locales field is required.',
                'locales.array' => 'The locales must be an array.',
                'locales.min' => 'At least one locale is required.',
                'locales.*.locale.required' => 'The locale is required.',
                'locales.*.locale.string' => 'The locale must be a string.',
                'locales.*.title.required' => 'The title is required.',
                'locales.*.title.string' => 'The title must be a string.',
                'locales.*.title.max' => 'The title may not be greater than 255 characters.',
                'file_path.required' => 'The file path is required.',
            ],
            'attributes' => [
                'file' => 'File',
                'locales' => 'Locales',
                'locales.*.locale' => 'Locale',
                'locales.*.title' => 'Title',
                'locales.*.description' => 'Description',
                'is_active' => 'Active',
                'sort_number' => 'Sort Number',
                'file_path' => 'File Path',
            ],
        ],
        'update' => [
            'messages' => [
                'file.file' => 'The file must be a file.',
                'file.max' => 'The file may not be greater than 10240 kilobytes.',
                'locales.array' => 'The locales must be an array.',
                'locales.min' => 'At least one locale is required.',
                'locales.*.locale.required' => 'The locale is required.',
                'locales.*.locale.string' => 'The locale must be a string.',
                'locales.*.title.required' => 'The title is required.',
                'locales.*.title.string' => 'The title must be a string.',
                'locales.*.title.max' => 'The title may not be greater than 255 characters.',
            ],
            'attributes' => [
                'file' => 'File',
                'locales' => 'Locales',
                'locales.*.locale' => 'Locale',
                'locales.*.title' => 'Title',
                'locales.*.description' => 'Description',
                'is_active' => 'Active',
                'sort_number' => 'Sort Number',
                'delete_file' => 'Delete File',
                'file_path' => 'File Path',
            ],
        ],
    ],
];
