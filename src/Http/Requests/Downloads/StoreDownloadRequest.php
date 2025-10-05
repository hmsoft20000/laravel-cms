<?php

namespace HMsoft\Cms\Http\Requests\Downloads;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Downloads\DownloadValidationRules;
use Illuminate\Database\Eloquent\Model;

class StoreDownloadRequest extends MyRequest
{
    use DownloadValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * We will automatically add the owner_type and map owner field here.
     */
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
        $rules = $this->getDownloadRules('create');
        $rules['owner_id'] = ['required', 'integer'];
        $rules['owner_type'] = ['required', 'string'];
        return $rules;
    }
}
