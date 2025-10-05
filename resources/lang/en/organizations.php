<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'image.file' => 'The file must be a file.',
                'role.exists' => 'The selected role does not exist.',
                'role_ids.array' => 'The role IDs must be an array.',
                'role_ids.*.required' => 'The role ID is required.',
                'role_ids.*.integer' => 'The role ID must be an integer.',
                'role_ids.*.exists' => 'The selected role does not exist.',
                'locales.required' => 'The translations are required.',
                'locales.array' => 'The translations must be an array.',
                'locales.*.locale.required' => 'The locale code is required.',
                'locales.*.name.string' => 'The name must be a string.',
                'locales.*.name.unique' => 'The name has already been taken.',
            ],
            'attributes' => [
                'image' => 'Image',
                'role' => 'Role ID',
                'role_ids' => 'Role IDs',
                'role_ids.*' => 'Role ID',
                'locales' => 'Translations',
                'locales.*.locale' => 'Locale Code',
                'locales.*.name' => 'Name',
            ],
        ],
        'update' => [
            'messages' => [
                'id.exists' => 'The selected organization does not exist.',
                'image.file' => 'The file must be a file.',
                'role.exists' => 'The selected role does not exist.',
                'role_ids.array' => 'The role IDs must be an array.',
                'role_ids.*.integer' => 'The role ID must be an integer.',
                'role_ids.*.exists' => 'The selected role does not exist.',
                'locales.array' => 'The translations must be an array.',
                'locales.*.locale.string' => 'The locale code must be a string.',
                'locales.*.name.string' => 'The name must be a string.',
            ],
            'attributes' => [
                'id' => 'Organization ID',
                'image' => 'Image',
                'role' => 'Role ID',
                'role_ids' => 'Role IDs',
                'role_ids.*' => 'Role ID',
                'locales' => 'Translations',
                'locales.*.locale' => 'Locale Code',
                'locales.*.name' => 'Name',
            ],
        ],
        'update_all' => [
            'messages' => [
                '*.id.exists' => 'The selected organization does not exist.',
                '*.image.file' => 'The file must be a file.',
                '*.role_ids.array' => 'The role IDs must be an array.',
                '*.role_ids.*.integer' => 'The role ID must be an integer.',
                '*.role_ids.*.exists' => 'The selected role does not exist.',
                '*.locales.array' => 'The translations must be an array.',
                '*.locales.*.locale.required' => 'The locale code is required.',
                '*.locales.*.name.string' => 'The name must be a string.',
            ],
            'attributes' => [
                '*.id' => 'Organization ID',
                '*.image' => 'Image',
                '*.role_ids' => 'Role IDs',
                '*.role_ids.*' => 'Role ID',
                '*.locales' => 'Translations',
                '*.locales.*.locale' => 'Locale Code',
                '*.locales.*.name' => 'Name',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'The selected organization does not exist.',
            ],
            'attributes' => [
                'id' => 'Organization ID',
            ],
        ],
    ],
];
