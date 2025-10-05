<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'name.string' => 'The name must be a string.',
                'name.required' => 'The name field is required.',
                'locales.array' => 'The translations must be an array.',
                'locales.*.locale.string' => 'The locale code must be a string.',
                'locales.*.job.string' => 'The job must be a string.',
                'locales.*.short_content.string' => 'The short content must be a string.',
                'social_links.array' => 'The social links must be an array.',
                'social_links.*.link.string' => 'The link must be a string.',
                'social_links.*.link.required' => 'The link is required.',
            ],
            'attributes' => [
                'name' => 'Name',
                'locales' => 'Translations',
                'locales.*.locale' => 'Locale Code',
                'locales.*.job' => 'Job',
                'locales.*.short_content' => 'Short Content',
                'social_links' => 'Social Links',
                'social_links.*.link' => 'Link',
            ],
        ],
        'update' => [
            'messages' => [
                'id.exists' => 'The selected team member does not exist.',
                'name.string' => 'The name must be a string.',
                'name.required' => 'The name field is required.',
                'locales.array' => 'The translations must be an array.',
                'locales.*.locale.string' => 'The locale code must be a string.',
                'locales.*.job.string' => 'The job must be a string.',
                'locales.*.short_content.string' => 'The short content must be a string.',
                'social_links.array' => 'The social links must be an array.',
                'social_links.*.link.string' => 'The link must be a string.',
                'social_links.*.link.required' => 'The link is required.',
            ],
            'attributes' => [
                'id' => 'Team Member ID',
                'name' => 'Name',
                'locales' => 'Translations',
                'locales.*.locale' => 'Locale Code',
                'locales.*.job' => 'Job',
                'locales.*.short_content' => 'Short Content',
                'social_links' => 'Social Links',
                'social_links.*.link' => 'Link',
            ],
        ],
        'update_all' => [
            'messages' => [
                '*.id.exists' => 'The selected team member does not exist.',
                '*.name.string' => 'The name must be a string.',
                '*.name.required' => 'The name field is required.',
                '*.locales.array' => 'The translations must be an array.',
                '*.locales.*.locale.string' => 'The locale code must be a string.',
                '*.locales.*.job.string' => 'The job must be a string.',
                '*.locales.*.short_content.string' => 'The short content must be a string.',
                '*.social_links.array' => 'The social links must be an array.',
                '*.social_links.*.link.string' => 'The link must be a string.',
                '*.social_links.*.link.required' => 'The link is required.',
            ],
            'attributes' => [
                '*.id' => 'Team Member ID',
                '*.name' => 'Name',
                '*.locales' => 'Translations',
                '*.locales.*.locale' => 'Locale Code',
                '*.locales.*.job' => 'Job',
                '*.locales.*.short_content' => 'Short Content',
                '*.social_links' => 'Social Links',
                '*.social_links.*.link' => 'Link',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'The selected team member does not exist.',
            ],
            'attributes' => [
                'id' => 'Team Member ID',
            ],
        ],
    ],
];
