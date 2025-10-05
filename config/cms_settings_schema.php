<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings Page Schema
    |--------------------------------------------------------------------------
    |
    | This file defines the structure of the settings page in the frontend.
    | The frontend will dynamically build the UI based on this schema.
    |
    | `column`: 'main' or 'sidebar'
    | `type`: 'text', 'email', 'tel', 'url', 'image', 'map'
    | `*_key`: Corresponds to a translation key in the frontend's language files.
    |
    */

    'cards' => [

        // --- Main Column Cards ---

        [
            'id' => 'basic_info',
            'title_key' => 'settings.cards.basic_info.title',
            'description_key' => 'settings.cards.basic_info.description',
            'column' => 'main',
            'fields' => [
                ['name' => 'company_name', 'type' => 'text', 'label_key' => 'settings.fields.company_name'],
                ['name' => 'company_email', 'type' => 'email', 'label_key' => 'settings.fields.company_email'],
                ['name' => 'company_phone', 'type' => 'tel', 'label_key' => 'settings.fields.company_phone'],
                ['name' => 'company_whatsapp_number', 'type' => 'tel', 'label_key' => 'settings.fields.company_whatsapp_number'],
            ],
        ],
        [
            'id' => 'location',
            'title_key' => 'settings.cards.location.title',
            'column' => 'main',
            'fields' => [
                ['name' => 'company_address', 'type' => 'text', 'label_key' => 'settings.fields.company_address'],
                // The map component in the frontend will use 'lat' and 'long' together
                // ['name' => 'lat', 'type' => 'map', 'label_key' => 'settings.fields.lat'],
                // ['name' => 'long', 'type' => 'map', 'label_key' => 'settings.fields.long'],

                [
                    'name' => 'lat',
                    'type' => 'text',
                    'label_key' => 'settings.fields.lat',
                    'disabled' => true
                ],
                [
                    'name' => 'long',
                    'type' => 'text',
                    'label_key' => 'settings.fields.long',
                    'disabled' => true
                ],

            ],
        ],
        [
            'id' => 'social_links',
            'title_key' => 'settings.cards.social_links.title',
            'description_key' => 'settings.cards.social_links.description',
            'column' => 'main',
            'fields' => [
                ['name' => 'facebook_link', 'type' => 'url', 'label_key' => 'settings.fields.facebook_link'],
                ['name' => 'twitter_link', 'type' => 'url', 'label_key' => 'settings.fields.twitter_link'],
                ['name' => 'linkedin_link', 'type' => 'url', 'label_key' => 'settings.fields.linkedin_link'],
                ['name' => 'instagram_link', 'type' => 'url', 'label_key' => 'settings.fields.instagram_link'],
                ['name' => 'youtube_link', 'type' => 'url', 'label_key' => 'settings.fields.youtube_link'],
                ['name' => 'google_plus_link', 'type' => 'url', 'label_key' => 'settings.fields.google_plus_link'],
                ['name' => 'vimeo_link', 'type' => 'url', 'label_key' => 'settings.fields.vimeo_link'],
                ['name' => 'pinterest_link', 'type' => 'url', 'label_key' => 'settings.fields.pinterest_link'],
            ],
        ],
        [
            'id' => 'currency_settings',
            'title_key' => 'settings.cards.currency_settings.title',
            'description_key' => 'settings.cards.currency_settings.description',
            'column' => 'main',
            'fields' => [
                ['name' => 'currency_symbol', 'type' => 'text', 'label_key' => 'settings.fields.currency_symbol'],
                ['name' => 'currency_symbol_position', 'type' => 'text', 'label_key' => 'settings.fields.currency_symbol_position'],
            ],
        ],
        [
            'id' => 'integrations',
            'title_key' => 'settings.cards.integrations.title',
            'description_key' => 'settings.cards.integrations.description',
            'column' => 'main',
            'fields' => [
                ['name' => 'telegram_boot_token', 'type' => 'text', 'label_key' => 'settings.fields.telegram_boot_token'],
            ],
        ],
        [
            'id' => 'additional_info',
            'title_key' => 'settings.cards.additional_info.title',
            'column' => 'main',
            'fields' => [
                ['name' => 'company_work_hours', 'type' => 'text', 'label_key' => 'settings.fields.company_work_hours'],
                ['name' => 'year_experiance', 'type' => 'text', 'label_key' => 'settings.fields.year_experiance'],
                ['name' => 'company_copyright_text', 'type' => 'text', 'label_key' => 'settings.fields.company_copyright_text'],
            ],
        ],

        // --- Sidebar Column Cards ---

        [
            'id' => 'branding',
            'title_key' => 'settings.cards.branding.title',
            'column' => 'sidebar',
            'fields' => [
                [
                    'name' => 'company_logo',
                    'type' => 'image',
                    'label_key' => 'settings.fields.company_logo',
                    'description_key' => 'settings.fields.company_logo_desc'
                ],
                [
                    'name' => 'company_fav_icon',
                    'type' => 'image',
                    'label_key' => 'settings.fields.company_fav_icon',
                    'description_key' => 'settings.fields.company_fav_icon_desc'
                ],
                [
                    'name' => 'footer_logo',
                    'type' => 'image',
                    'label_key' => 'settings.fields.footer_logo',
                    'description_key' => 'settings.fields.footer_logo_desc'
                ],
            ],
        ],

    ],
];
