<?php

namespace HMsoft\Cms\Http\Requests\Sector;

use HMsoft\Cms\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

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

    public function prepareForValidation()
    {
        $this->merge([
            'id' =>  $this->route()?->originalParameter('sector'),
        ]);
    }

    public function messages()
    {
        $file = 'cms.sectors.validation.delete.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.sectors.validation.delete.attributes';
        return is_array(trans($file)) ? trans($file) : [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => [Rule::exists('sectors', 'id')],
        ];
    }
}
