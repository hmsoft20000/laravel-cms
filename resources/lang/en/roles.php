<?php

return [
    'validation' => [
        'update_all' => [
            'messages' => [
                '*.required' => 'The data is required.',
                '*.array' => 'The data must be an array.',
                '*.id.required' => 'The role ID is required.',
                '*.id.integer' => 'The role ID must be an integer.',
                '*.id.exists' => 'The selected role does not exist.',
                '*.name.required' => 'The role name is required.',
                '*.name.string' => 'The role name must be a string.',
                '*.name.max' => 'The role name must be less than 255 characters.',
                '*.slug.required' => 'The role slug is required.',
                '*.slug.string' => 'The role slug must be a string.',
                '*.slug.max' => 'The role slug must be less than 255 characters.',
                '*.description.string' => 'The role description must be a string.',
                '*.level.required' => 'The role level is required.',
                '*.level.integer' => 'The role level must be an integer.',
                '*.level.min' => 'The role level must be at least 0.',
                '*.parent_id.integer' => 'The parent role ID must be an integer.',
                '*.parent_id.exists' => 'The selected parent role does not exist.',
                '*.permission_ids.array' => 'The permission IDs must be an array.',
                '*.permission_ids.*.integer' => 'The permission ID must be an integer.',
                '*.permission_ids.*.exists' => 'The selected permission does not exist.',
            ],
            'attributes' => [
                '*' => 'Data',
                '*.id' => 'Role ID',
                '*.name' => 'Role Name',
                '*.slug' => 'Role Slug',
                '*.description' => 'Role Description',
                '*.level' => 'Role Level',
                '*.parent_id' => 'Parent Role ID',
                '*.permission_ids' => 'Permission IDs',
                '*.permission_ids.*' => 'Permission ID',
            ],
        ],
    ],
];
