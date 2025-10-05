<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'image.file' => 'The file must be a file.',
                'work_ratio.numeric' => 'The work ratio must be a number.',
                'locales.required' => 'The translations are required.',
                'locales.array' => 'The translations must be an array.',
                'locales.min' => 'At least one translation must be provided.',
                'locales.*.locale.required' => 'The locale code is required.',
                'locales.*.short_content.string' => 'The short content must be a string.',
                'locales.*.name.string' => 'The name must be a string.',
                'locales.*.name.unique' => 'The name has already been taken.',
            ],
            'attributes' => [
                'image' => 'Image',
                'work_ratio' => 'Work Ratio',
                'locales' => 'Translations',
                'locales.*.locale' => 'Locale Code',
                'locales.*.short_content' => 'Short Content',
                'locales.*.name' => 'Name',
            ],
        ],
        'update' => [
            'messages' => [
                'image.file' => 'The file must be a file.',
                'work_ratio.numeric' => 'The work ratio must be a number.',
                'locales.required' => 'The translations are required.',
                'locales.array' => 'The translations must be an array.',
                'locales.min' => 'At least one translation must be provided.',
                'locales.*.locale.required' => 'The locale code is required.',
                'locales.*.short_content.string' => 'The short content must be a string.',
                'locales.*.name.string' => 'The name must be a string.',
                'locales.*.name.unique' => 'The name has already been taken.',
            ],
            'attributes' => [
                'image' => 'Image',
                'work_ratio' => 'Work Ratio',
                'locales' => 'Translations',
                'locales.*.locale' => 'Locale Code',
                'locales.*.short_content' => 'Short Content',
                'locales.*.name' => 'Name',
            ],
        ],
        'update_all' => [
            'messages' => [
                '*.required' => 'The data is required.',
                '*.array' => 'The data must be an array.',
                '*.id.required' => 'The sector ID is required.',
                '*.id.integer' => 'The sector ID must be an integer.',
                '*.id.exists' => 'The selected sector does not exist.',
                '*.image.file' => 'The file must be a file.',
                '*.work_ratio.numeric' => 'The work ratio must be a number.',
                '*.locales.array' => 'The translations must be an array.',
                '*.locales.min' => 'At least one translation must be provided.',
                '*.locales.*.locale.required' => 'The locale code is required.',
                '*.locales.*.short_content.string' => 'The short content must be a string.',
                '*.locales.*.name.string' => 'The name must be a string.',
            ],
            'attributes' => [
                '*' => 'Data',
                '*.id' => 'Sector ID',
                '*.image' => 'Image',
                '*.work_ratio' => 'Work Ratio',
                '*.locales' => 'Translations',
                '*.locales.*.locale' => 'Locale Code',
                '*.locales.*.short_content' => 'Short Content',
                '*.locales.*.name' => 'Name',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'The selected sector does not exist.',
            ],
            'attributes' => [
                'id' => 'Sector ID',
            ],
        ],
    ],
];
