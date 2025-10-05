<?php

namespace HMsoft\Cms\Traits\Media;

use HMsoft\Cms\Rules\FileOrUrl;

trait ValidatesSingleFile
{
    protected function getSingleFileValidationRules(): array
    {
        return [
            'file' => ['sometimes', 'nullable', new FileOrUrl],
            'delete_file' => ['sometimes', 'boolean'],
        ];
    }
}
