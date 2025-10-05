<?php

namespace HMsoft\Cms\Traits\Media;

trait ValidatesSingleMedia
{
    /**
     * Get the validation rules for a single image upload field.
     *
     * @return array
     */
    protected function getSingleImageValidationRules(): array
    {
        return [
            // Rule for the uploaded image file
            'image' => ['sometimes', 'nullable', 'image', 'max:2048'], // Max 2MB

            // Rule for the flag to delete an existing image
            'delete_image' => ['sometimes', 'boolean'],
        ];
    }
}
