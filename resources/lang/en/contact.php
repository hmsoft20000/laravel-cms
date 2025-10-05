<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'name.string' => 'The name field must be a string.',
                'name.max' => 'The name field must be less than 191 characters.',
                'email.email' => 'The email field must be a valid email address.',
                'email.max' => 'The email field must be less than 191 characters.',
                'mobile.string' => 'The mobile field must be a string.',
                'mobile.max' => 'The mobile field must be less than 191 characters.',
                'residence.string' => 'The residence field must be a string.',
                'residence.max' => 'The residence field must be less than 191 characters.',
                'nationality.string' => 'The nationality field must be a string.',
                'nationality.max' => 'The nationality field must be less than 191 characters.',
                'description.string' => 'The description field must be a string.',
                'message.string' => 'The message field must be a string.',
                'subject.string' => 'The subject field must be a string.',
                'subject.max' => 'The subject field must be less than 191 characters.',
                'file-upload.array' => 'The file uploads must be an array.',
                'file-upload.*.file' => 'The uploaded file must be a file.',
                'file-upload.*.mimes' => 'The file type is not supported.',
                'file-upload.*.max' => 'The file size must be less than 10MB.',
            ],
            'attributes' => [
                'name' => 'Name',
                'email' => 'Email',
                'mobile' => 'Mobile',
                'residence' => 'Residence',
                'nationality' => 'Nationality',
                'description' => 'Description',
                'message' => 'Message',
                'subject' => 'Subject',
                'file-upload' => 'File Uploads',
                'file-upload.*' => 'Uploaded File',
            ],
        ],
        'update' => [
            'messages' => [
                'status.required' => 'The status field is required.',
                'status.in' => 'The status field must be read or unread.',
                'is_starred.required' => 'The starred status is required.',
                'is_starred.boolean' => 'The starred status must be true or false.',
            ],
            'attributes' => [
                'status' => 'Status',
                'is_starred' => 'Starred',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'The selected contact message does not exist.',
            ],
            'attributes' => [
                'id' => 'Contact Message ID',
            ],
        ],
        'reply' => [
            'messages' => [
                'reply_message.required' => 'The reply message field is required.',
                'reply_message.string' => 'The reply message field must be a string.',
                'reply_message.min' => 'The reply message must be at least 10 characters.',
            ],
            'attributes' => [
                'reply_message' => 'Reply Message',
            ],
        ],
    ],
];
