<?php

namespace HMsoft\Cms\Traits\Testimonial;

trait TestimonialValidationRule
{

    /**
     * Get the shared validation rules for a plan based on the context.
     *
     * @param string $context The context of the validation ('create' or 'update').
     * @return array
     */
    protected function getTestimonialRules(string $context = 'update'): array
    {
        $rules = [
            'rate' => 'sometimes',
            'sort_number' => 'sometimes',
            'is_active' => 'sometimes',
        ];

        switch ($context) {
            case 'create':
                $rules['name'] =    ['required'];
                $rules['message'] = ['required'];
                break;
            case 'update':
                $rules['name'] =    ['sometimes', 'required'];
                $rules['message'] = ['sometimes', 'required'];
                break;

            default:
                break;
        }

        return $rules;
    }
}
