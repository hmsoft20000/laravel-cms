<?php

namespace HMsoft\Cms\Traits\Media;

use HMsoft\Cms\Rules\FileOrUrl;

trait MediaValidationRules
{
    /**
     * Get the validation rules for updating a collection of media items.
     * These rules are typically for reordering or setting a default image.
     */
    protected function getUpdateAllMediaRules(): array
    {
        return [
            'media' => ['required', 'array', 'min:1'],
            'media.*.id' => ['required', 'integer'],
            'media.*.is_default' => ['sometimes'],
            'media.*.sort_number' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    /**
     * Get the validation rules for uploading new media files.
     */
    protected function getUploadMediaRules(): array
    {
        return [
            'media' => ['required', 'array', 'min:1'],
            'media.*' => ['required', 'array'], // كل عنصر يجب أن يكون object/array
            'media.*.file' => ['required', new FileOrUrl], // <-- استخدام القاعدة المخصصة
            'media.*.is_default' => ['sometimes'],
            'media.*.sort_number' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
