<?php

namespace HMsoft\Cms\Http\Requests\Sector;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Lang;
use Illuminate\Validation\Rule;

class UpdateAll extends MyRequest
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
        $validLocales = Lang::pluck('locale')->toArray();
        $data = $this->all();

        // Process each sector item in the array
        foreach ($data as $index => $item) {
            // Handle locales for each item
            if (isset($item['locales'])) {
                $data[$index]['locales'] = collect($item['locales'])
                    ->filter(fn($locale) => in_array($locale['locale'] ?? null, $validLocales))
                    ->values()->all();
            }
        }

        $this->merge($data);
    }

    public function messages()
    {
        $file = 'cms.sectors.validation.update_all.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.sectors.validation.update_all.attributes';
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
            '*' => ['required', 'array'],
            '*.id' => ['required', 'integer', 'exists:sectors,id'],
            '*.image' => ['sometimes', 'nullable', 'file'],
            '*.work_ratio' => ['sometimes', 'nullable'],
            '*.sort_number' => ['sometimes', 'integer', 'min:0'],
            '*.delete_image' => ['sometimes', 'boolean'],
            '*.locales' => ['sometimes', 'array', 'min:1'],
            '*.locales.*.locale' => ['sometimes', 'required'],
            '*.locales.*.short_content' => ['sometimes', 'nullable', 'string'],
            '*.locales.*.name' => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            foreach ($data as $index => $item) {
                if (isset($item['locales'])) {
                    $hasAtLeastOneName = collect($item['locales'])
                        ->contains(fn($locale) => !empty($locale['name']));

                    if (! $hasAtLeastOneName) {
                        $validator->errors()->add(
                            "{$index}.locales.*.name",
                            trans('sectors.validation.update_all.at_least_one_name')
                        );
                    }
                }
            }
        });
    }
}
