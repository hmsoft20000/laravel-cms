<?php

namespace HMsoft\Cms\Http\Requests\Downloads;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Downloads\DownloadValidationRules;

class UpdateDownloadRequest extends MyRequest
{
    use DownloadValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    public function rules(): array
    {
        return $this->getDownloadRules('update');
    }
}
