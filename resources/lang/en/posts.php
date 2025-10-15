<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'type.required' => 'The type field is required.',
                'show_in_footer.boolean' => 'The show in footer field must be true or false.',
                'show_in_header.boolean' => 'The show in header field must be true or false.',
                'is_active.boolean' => 'The is active field must be true or false.',
            ],
            'attributes' => [
                'type' => 'Type',
                'show_in_footer' => 'Show in footer',
                'show_in_header' => 'Show in header',
                'is_active' => 'Active',
                'category_ids' => 'Category IDs',
                'partner_ids' => 'Partner IDs',
                'sponsor_ids' => 'Sponsor IDs',
                'features' => 'Features',
                'downloads' => 'Downloads',
                'attributes' => 'Attributes',
            ],
        ],
        'update' => [
            'messages' => [
                'show_in_footer.boolean' => 'The show in footer field must be true or false.',
                'show_in_header.boolean' => 'The show in header field must be true or false.',
                'is_active.boolean' => 'The is active field must be true or false.',
            ],
            'attributes' => [
                'type' => 'Type',
                'show_in_footer' => 'Show in footer',
                'show_in_header' => 'Show in header',
                'is_active' => 'Active',
                'category_ids' => 'Category IDs',
                'partner_ids' => 'Partner IDs',
                'sponsor_ids' => 'Sponsor IDs',
                'features' => 'Features',
                'downloads' => 'Downloads',
                'attributes' => 'Attributes',
            ],
        ],
        'updateAll' => [
            'messages' => [
                '*.id.required' => 'The ID is required.',
                '*.id.integer' => 'The ID must be an integer.',
                '*.show_in_footer.boolean' => 'The show in footer field must be true or false.',
                '*.show_in_header.boolean' => 'The show in header field must be true or false.',
                '*.is_active.boolean' => 'The is active field must be true or false.',
            ],
            'attributes' => [
                '*.id' => 'ID',
                '*.show_in_footer' => 'Show in footer',
                '*.show_in_header' => 'Show in header',
                '*.is_active' => 'Active',
                '*.category_ids' => 'Category IDs',
                '*.partner_ids' => 'Partner IDs',
                '*.sponsor_ids' => 'Sponsor IDs',
                '*.features' => 'Features',
                '*.downloads' => 'Downloads',
                '*.attributes' => 'Attributes',
            ],
        ],
        'upload_media' => [
            'messages' => [
                'file.required' => 'The file is required.',
                'file.file' => 'The file must be a file.',
                'file.mimes' => 'The file must be a file of type: :values.',
                'media_type.required' => 'The media type is required.',
                'media_type.string' => 'The media type must be a string.',
                'media_type.in' => 'The selected media type is invalid.',
                'sort_number.integer' => 'The sort number must be an integer.',
                'is_default.boolean' => 'The is default field must be true or false.',
            ],
            'attributes' => [
                'file' => 'File',
                'media_type' => 'Media Type',
                'sort_number' => 'Sort Number',
                'is_default' => 'Is Default',
            ],
        ],
        'delete_media' => [
            'messages' => [
                'media_id.required' => 'The media ID is required.',
                'media_id.integer' => 'The media ID must be an integer.',
                'media_id.exists' => 'The selected media is invalid.',
            ],
            'attributes' => [
                'media_id' => 'Media ID',
            ],
        ],
        'update_media_all' => [
            'messages' => [
                'media.required' => 'The media field is required.',
                'media.array' => 'The media field must be an array.',
                'media.*.id.required' => 'The media ID is required.',
                'media.*.id.integer' => 'The media ID must be an integer.',
                'media.*.id.exists' => 'The selected media is invalid.',
                'media.*.sort_number.integer' => 'The sort number must be an integer.',
                'media.*.is_default.boolean' => 'The is default field must be true or false.',
            ],
            'attributes' => [
                'media' => 'Media',
                'media.*.id' => 'Media ID',
                'media.*.sort_number' => 'Sort Number',
                'media.*.is_default' => 'Is Default',
            ],
        ],
    ],
];
