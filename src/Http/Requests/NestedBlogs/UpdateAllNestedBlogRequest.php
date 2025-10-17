<?php

namespace HMsoft\Cms\Http\Requests\NestedBlogs;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Content\Blog;
use HMsoft\Cms\Traits\Blogs\BlogValidationRules;

class UpdateAllNestedBlogRequest extends MyRequest
{
    use BlogValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request for a bulk update.
     */
    public function rules(): array
    {
        $singleRules = $this->getBlogRules('update');
        $tableName = resolve(Blog::class)->getTable();

        $rulesForAll = [
            '*' => ['required', 'array'],
            '*.id' => ['required', 'integer', 'exists:' . $tableName . ',id'],
        ];

        // Apply the single blog update rules to each item in the array.
        foreach ($singleRules as $field => $rule) {
            $rulesForAll['*.' . $field] = $rule;
        }

        return $rulesForAll;
    }
}
