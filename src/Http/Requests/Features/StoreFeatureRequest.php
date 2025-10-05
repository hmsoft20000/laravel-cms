<?php

namespace HMsoft\Cms\Http\Requests\Features;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Features\FeatureValidationRules;
use Illuminate\Database\Eloquent\Model;

class StoreFeatureRequest extends MyRequest
{
    use FeatureValidationRules;

    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation(): void
    {

        $owner = $this->route('owner');
        if ($owner instanceof Model) {
            $this->merge([
                'owner_type' => $owner->getMorphClass(),
                'owner_id' => $owner->id,
            ]);
        }

        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    public function rules(): array
    {

        $rules = $this->getFeatureRules('create');
        $rules['owner_id'] = ['required', 'integer'];
        $rules['owner_type'] = ['required', 'string'];
        return $rules;
    }
}
