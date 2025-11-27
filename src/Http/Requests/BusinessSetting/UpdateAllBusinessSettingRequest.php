<?php

namespace HMsoft\Cms\Http\Requests\BusinessSetting;

use HMsoft\Cms\Http\Requests\MyRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAllBusinessSettingRequest extends MyRequest
{
    static $imageKeys = [
        'company_logo',
        'default_category_image',
        'default_item_image',
        'default_user_image',
        'company_fav_icon',
        'footer_logo',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->is_admin;
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        // Process each business setting item in the array
        foreach ($data as $index => $item) {
            $deleteFlags = [];
            foreach (self::$imageKeys as $key) {
                $deleteFlags[] = "{$key}_delete_image";
            }

            foreach ($deleteFlags as $flag) {
                if (isset($item[$flag])) {
                    $data[$index][$flag] = filter_var($item[$flag], FILTER_VALIDATE_BOOLEAN);
                }
            }
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        $baseRules = [
            'company_phone' => ['string', 'nullable'],
            'company_address' => ['string', 'nullable'],
            'company_name' => ['string', 'nullable'],
            'company_copyright_text' => ['string', 'nullable'],
            'lat' => ['string', 'nullable'],
            'long' => ['string', 'nullable'],
            'facebook_link' => ['string', 'url', 'nullable'],
            'twitter_link' => ['string', 'url', 'nullable'],
            'google_plus_link' => ['string', 'url', 'nullable'],
            'vimeo_link' => ['string', 'url', 'nullable'],
            'youtube_link' => ['string', 'url', 'nullable'],
            'pinterest_link' => ['string', 'url', 'nullable'],
            'linkedin_link' => ['string', 'url', 'nullable'],
            'instagram_link' => ['string', 'url', 'nullable'],
            'company_email' => ['string', 'email', 'nullable'],
            'company_whatsapp_number' => ['string', 'nullable'],
            'year_experiance' => ['string', 'nullable'],
            'currency_symbol' => ['string', 'nullable'],
            'currency_symbol_position' => ['string', 'nullable'],
            'company_work_hours' => ['string', 'nullable'],
            'telegram_boot_token' => ['string', 'nullable'],
        ];

        foreach (self::$imageKeys as $key) {
            $baseRules[$key] = ['file', 'nullable'];
            $baseRules["{$key}_delete_image"] = ['boolean', 'nullable'];
        }

        // Add 'sometimes' to all rules and wrap with array structure
        $rules = [
            '*' => ['required', 'array'],
        ];

        foreach ($baseRules as $key => $value) {
            array_unshift($value, 'sometimes');
            $rules["*.$key"] = $value;
        }

        return $rules;
    }

    public function messages()
    {
        $file = 'cms.business_settings.validation.update_all.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.business_settings.validation.update_all.attributes';
        return is_array(trans($file)) ? trans($file) : [];
    }
}
