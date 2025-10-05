<?php

namespace HMsoft\Cms\Http\Requests\Statistics;

use HMsoft\Cms\Http\Requests\MyRequest;

class Delete extends MyRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // No additional validation rules needed for delete
        ];
    }
}
