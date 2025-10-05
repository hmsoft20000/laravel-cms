<?php

namespace HMsoft\Cms\Http\Requests\Media;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Media\MediaValidationRules;

class UpdateMediaAllRequest extends MyRequest
{
    use MediaValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->getUpdateAllMediaRules();
    }

    // Your custom validator to ensure only one default is great!
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $defaultCount = collect($this->input('media', []))
                ->where('is_default', true)
                ->count();

            if ($defaultCount > 1) {
                $validator->errors()->add('media', 'Only one media file can be set as the default.');
            }
        });
    }
}
