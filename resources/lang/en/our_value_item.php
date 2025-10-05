<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'image.file' => 'The file must be a file.',
                'locales.required' => 'The translations are required.',
                'locales.array' => 'The translations must be an array.',
                'locales.*.locale.required' => 'The locale code is required.',
                'locales.*.title.string' => 'The title must be a string.',
                'locales.*.slug.string' => 'The slug must be a string.',
                'locales.*.slug.unique' => 'The slug has already been taken.',
                'locales.*.sub_title.string' => 'The sub title must be a string.',
                'locales.*.meta_title.string' => 'The meta title must be a string.',
                'locales.*.meta_description.string' => 'The meta description must be a string.',
                'meta_keywords.array' => 'The meta keywords must be an array.',
                'meta_keywords.*.string' => 'The meta keyword must be a string.',
            ],
            'attributes' => [
                'image' => 'Image',
                'locales' => 'Translations',
                'locales.*.locale' => 'Locale Code',
                'locales.*.title' => 'Title',
                'locales.*.slug' => 'Slug',
                'locales.*.sub_title' => 'Sub Title',
                'locales.*.meta_title' => 'Meta Title',
                'locales.*.meta_description' => 'Meta Description',
                'meta_keywords' => 'Meta Keywords',
                'meta_keywords.*' => 'Meta Keyword',
            ],
        ],
        'update' => [
            'messages' => [
                'id.exists' => 'The selected value item does not exist.',
                'image.file' => 'The file must be a file.',
                'locales.array' => 'The translations must be an array.',
                'locales.*.locale.required' => 'The locale code is required.',
                'locales.*.title.string' => 'The title must be a string.',
                'locales.*.slug.string' => 'The slug must be a string.',
                'locales.*.slug.unique' => 'The slug has already been taken.',
                'locales.*.sub_title.string' => 'The sub title must be a string.',
                'locales.*.meta_title.string' => 'The meta title must be a string.',
                'locales.*.meta_description.string' => 'The meta description must be a string.',
                'meta_keywords.array' => 'The meta keywords must be an array.',
                'meta_keywords.*.string' => 'The meta keyword must be a string.',
            ],
            'attributes' => [
                'id' => 'Value Item ID',
                'image' => 'Image',
                'locales' => 'Translations',
                'locales.*.locale' => 'Locale Code',
                'locales.*.title' => 'Title',
                'locales.*.slug' => 'Slug',
                'locales.*.sub_title' => 'Sub Title',
                'locales.*.meta_title' => 'Meta Title',
                'locales.*.meta_description' => 'Meta Description',
                'meta_keywords' => 'Meta Keywords',
                'meta_keywords.*' => 'Meta Keyword',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'The selected value item does not exist.',
            ],
            'attributes' => [
                'id' => 'Value Item ID',
            ],
        ],
    ],
];
