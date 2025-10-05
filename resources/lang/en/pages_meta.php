<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'name.required' => 'The name field is required.',
                'name.unique' => 'The name has already been taken.',
                'locales.required' => 'The translations are required.',
                'locales.array' => 'The translations must be an array.',
                'locales.*.locale.required' => 'The locale code is required.',
                'locales.*.title.string' => 'The title must be a string.',
                'locales.*.description.string' => 'The description must be a string.',
                'locales.*.keywords.string' => 'The keywords must be a string.',
            ],
            'attributes' => [
                'name' => 'Name',
                'locales' => 'Translations',
                'locales.*.locale' => 'Locale Code',
                'locales.*.title' => 'Title',
                'locales.*.description' => 'Description',
                'locales.*.keywords' => 'Keywords',
            ],
        ],
        'update' => [
            'messages' => [
                'id.exists' => 'The selected page meta does not exist.',
                'name.required' => 'The name field is required.',
                'name.unique' => 'The name has already been taken.',
                'locales.required' => 'The translations are required.',
                'locales.array' => 'The translations must be an array.',
                'locales.*.locale.required' => 'The locale code is required.',
                'locales.*.locale.string' => 'The locale code must be a string.',
                'locales.*.title.string' => 'The title must be a string.',
                'locales.*.title.max' => 'The title must be less than 255 characters.',
                'locales.*.description.string' => 'The description must be a string.',
                'locales.*.keywords.string' => 'The keywords must be a string.',
                'locales.*.keywords.max' => 'The keywords must be less than 255 characters.',
            ],
            'attributes' => [
                'id' => 'Page Meta ID',
                'name' => 'Name',
                'locales' => 'Translations',
                'locales.*.locale' => 'Locale Code',
                'locales.*.title' => 'Title',
                'locales.*.description' => 'Description',
                'locales.*.keywords' => 'Keywords',
            ],
        ],
        'update_all' => [
            'messages' => [
                'pages.required' => 'The pages are required.',
                'pages.array' => 'The pages must be an array.',
                'pages.min' => 'At least one page must be provided.',
                'pages.*.id.required' => 'The page ID is required.',
                'pages.*.id.exists' => 'The selected page does not exist.',
                'pages.*.translations.required' => 'The page translations are required.',
                'pages.*.translations.array' => 'The page translations must be an array.',
                'pages.*.translations.*.title.string' => 'The page title must be a string.',
                'pages.*.translations.*.title.max' => 'The page title must be less than 255 characters.',
                'pages.*.translations.*.description.string' => 'The page description must be a string.',
                'pages.*.translations.*.keywords.string' => 'The page keywords must be a string.',
                'pages.*.translations.*.keywords.max' => 'The page keywords must be less than 255 characters.',
            ],
            'attributes' => [
                'pages' => 'Pages',
                'pages.*.id' => 'Page ID',
                'pages.*.translations' => 'Page Translations',
                'pages.*.translations.*.title' => 'Page Title',
                'pages.*.translations.*.description' => 'Page Description',
                'pages.*.translations.*.keywords' => 'Page Keywords',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'The selected page meta does not exist.',
            ],
            'attributes' => [
                'id' => 'Page Meta ID',
            ],
        ],
    ],
];
