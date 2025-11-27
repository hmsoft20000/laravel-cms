<?php

namespace HMsoft\Cms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MyRequest extends FormRequest
{

    /**
     * When we fail validation, override our default error.
     * @param \Illuminate\Contracts\Validation\Validator $validator
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {

        $errors = $validator->errors()->all();

        $firstError = collect($errors)
            ->flatten()
            ->first() ?? trans('cms.validation.error');

        $allMessagesCount = collect($errors)->flatten()->count();
        $additionalCount = $allMessagesCount - 1;

        $customMessage = $firstError;
        if ($additionalCount > 0) {
            $customMessage .= ' ' . trans('cms.validation.more_errors', ['count' => $additionalCount]);
        }

        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            errorResponse(
                $customMessage,
                422,
                $validator->errors()->toArray()
            )
        );
    }
}
