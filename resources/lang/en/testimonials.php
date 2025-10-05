<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'image.file' => 'The file must be a file.',
                'name.string' => 'The name must be a string.',
                'job.string' => 'The job must be a string.',
                'message.string' => 'The message must be a string.',
                'rate.string' => 'The rate must be a string.',
            ],
            'attributes' => [
                'image' => 'Image',
                'name' => 'Name',
                'job' => 'Job',
                'message' => 'Message',
                'rate' => 'Rate',
            ],
        ],
        'update' => [
            'messages' => [
                'id.exists' => 'The selected testimonial does not exist.',
                'name.string' => 'The name must be a string.',
                'job.string' => 'The job must be a string.',
                'message.string' => 'The message must be a string.',
                'rate.string' => 'The rate must be a string.',
            ],
            'attributes' => [
                'id' => 'Testimonial ID',
                'name' => 'Name',
                'job' => 'Job',
                'message' => 'Message',
                'rate' => 'Rate',
            ],
        ],
        'update_all' => [
            'messages' => [
                '*.required' => 'The data is required.',
                '*.array' => 'The data must be an array.',
                '*.id.required' => 'The testimonial ID is required.',
                '*.id.integer' => 'The testimonial ID must be an integer.',
                '*.id.exists' => 'The selected testimonial does not exist.',
                '*.name.string' => 'The name must be a string.',
                '*.job.string' => 'The job must be a string.',
                '*.message.string' => 'The message must be a string.',
                '*.rate.string' => 'The rate must be a string.',
            ],
            'attributes' => [
                '*' => 'Data',
                '*.id' => 'Testimonial ID',
                '*.name' => 'Name',
                '*.job' => 'Job',
                '*.message' => 'Message',
                '*.rate' => 'Rate',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'The selected testimonial does not exist.',
            ],
            'attributes' => [
                'id' => 'Testimonial ID',
            ],
        ],
    ],
];
