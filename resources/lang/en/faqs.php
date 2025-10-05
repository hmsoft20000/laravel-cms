<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'owner_id.required' => 'The owner ID field is required.',
                'owner_id.integer' => 'The owner ID field must be an integer.',
                'owner_id.exists' => 'The selected owner does not exist.',
                'owner_type.required' => 'The owner type field is required.',
                'owner_type.string' => 'The owner type field must be a string.',
                'owner_type.in' => 'The owner type field must be post.',
                'is_active.boolean' => 'The active status must be true or false.',
                'sort_number.required' => 'The sort number field is required.',
                'sort_number.integer' => 'The sort number field must be an integer.',
                'locales.required' => 'The translations are required.',
                'locales.array' => 'The translations must be an array.',
                'locales.min' => 'At least one translation must be provided.',
                'locales.*.locale.required' => 'The locale code is required.',
                'locales.*.locale.string' => 'The locale code must be a string.',
                'locales.*.question.string' => 'The question must be a string.',
                'locales.*.question.max' => 'The question must be less than 255 characters.',
                'locales.*.answer.string' => 'The answer must be a string.',
            ],
            'attributes' => [
                'owner_id' => 'Owner ID',
                'owner_type' => 'Owner Type',
                'is_active' => 'Active Status',
                'sort_number' => 'Sort Number',
                'locales' => 'Translations',
                'locales.*.locale' => 'Locale Code',
                'locales.*.question' => 'Question',
                'locales.*.answer' => 'Answer',
            ],
        ],
        'update' => [
            'messages' => [
                'is_active.boolean' => 'The active status must be true or false.',
                'sort_number.integer' => 'The sort number field must be an integer.',
                'locales.array' => 'The translations must be an array.',
                'locales.min' => 'At least one translation must be provided.',
                'locales.*.locale.required' => 'The locale code is required.',
                'locales.*.locale.string' => 'The locale code must be a string.',
                'locales.*.question.string' => 'The question must be a string.',
                'locales.*.question.max' => 'The question must be less than 255 characters.',
                'locales.*.answer.string' => 'The answer must be a string.',
            ],
            'attributes' => [
                'is_active' => 'Active Status',
                'sort_number' => 'Sort Number',
                'locales' => 'Translations',
                'locales.*.locale' => 'Locale Code',
                'locales.*.question' => 'Question',
                'locales.*.answer' => 'Answer',
            ],
        ],
    ],
];
