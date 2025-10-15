<?php

namespace HMsoft\Cms\Http\Requests\NestedBlogs;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Blogs\BlogValidationRules;

class UpdateNestedBlogRequest extends MyRequest
{
    use BlogValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // You can add authorization logic here later.
        // For example: return $this->user()->can('update', $this->route('blog'));
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return $this->getBlogRules('update');
    }
}
