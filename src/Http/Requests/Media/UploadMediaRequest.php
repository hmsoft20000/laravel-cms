<?php

namespace HMsoft\Cms\Http\Requests\Media;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Media\MediaValidationRules;

class UploadMediaRequest extends MyRequest
{
    use MediaValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->getUploadMediaRules();
    }
}
