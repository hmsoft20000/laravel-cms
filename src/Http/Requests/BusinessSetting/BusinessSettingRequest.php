<?php

namespace HMsoft\Cms\Http\Requests\BusinessSetting;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\General\HasAuthService;

class BusinessSettingRequest extends MyRequest
{

    use HasAuthService;

    static $imageKeys = [
        'company_logo',
        'default_category_image',
        'default_item_image',
        'default_user_image',
        'company_fav_icon',
        'footer_logo',
    ];

    /**
     * Get all field names from the settings schema configuration
     *
     * @return array
     */
    protected function getAllSettingsFields(): array
    {
        $schema = config('cms_settings_schema.cards', []);
        $fields = [];

        foreach ($schema as $card) {
            if (isset($card['fields']) && is_array($card['fields'])) {
                foreach ($card['fields'] as $field) {
                    if (isset($field['name'])) {
                        $fields[] = $field['name'];
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * Get validation rules based on field type from schema
     *
     * @param string $fieldName
     * @return array
     */
    protected function getValidationRulesForField(string $fieldName): array
    {
        $schema = config('cms_settings_schema.cards', []);
        
        // Find the field in schema to get its type
        foreach ($schema as $card) {
            if (isset($card['fields']) && is_array($card['fields'])) {
                foreach ($card['fields'] as $field) {
                    if (isset($field['name']) && $field['name'] === $fieldName) {
                        $type = $field['type'] ?? 'text';
                        
                        switch ($type) {
                            case 'email':
                                return ['string', 'email', 'nullable'];
                            case 'url':
                                return ['string', 'url', 'nullable'];
                            case 'tel':
                                return ['string', 'nullable'];
                            case 'image':
                                return ['file', 'nullable'];
                            case 'text':
                            default:
                                return ['string', 'nullable'];
                        }
                    }
                }
            }
        }

        // Default validation for fields not found in schema
        return ['string', 'nullable'];
    }


    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {

        $deleteFlags = [];
        foreach (self::$imageKeys as $key) {
            $deleteFlags[] = "{$key}_delete_image";
        }

        foreach ($deleteFlags as $flag) {
            if ($this->has($flag)) {
                $this->merge([
                    $flag => filter_var($this->input($flag), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->isAuthenticated() && $this->getAuthenticatedUser()?->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * This method dynamically generates validation rules based on the settings schema configuration.
     * All field names and their validation rules are automatically extracted from the schema,
     * making it easy to maintain and ensuring consistency between frontend and backend validation.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $baseRules = [];
        
        // Get all fields from the settings schema configuration
        $schemaFields = $this->getAllSettingsFields();
        
        // Add validation rules for each field based on its type from schema
        foreach ($schemaFields as $fieldName) {
            $baseRules[$fieldName] = $this->getValidationRulesForField($fieldName);
        }

        // Add image delete flags for image fields
        foreach (self::$imageKeys as $key) {
            $baseRules["{$key}_delete_image"] = ['boolean', 'nullable'];
        }

        // Add 'sometimes' to all rules to make them optional
        $rules = [];
        foreach ($baseRules as $key => $value) {
            array_unshift($value, 'sometimes');
            $rules[$key] = $value;
        }

        return $rules;
    }
}
