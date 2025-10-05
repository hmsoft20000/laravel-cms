<?php

namespace HMsoft\Cms\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class FileOrUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        // Check if it's a valid uploaded file
        if ($value instanceof UploadedFile && $value->isValid()) {
            return; // It's a valid file, pass.
        }

        // Check if it's a valid URL string
        if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
            return; // It's a valid URL, pass.
        }

        // If neither, fail the validation.
        $fail('The :attribute must be a valid file or a URL.');
    }
}
