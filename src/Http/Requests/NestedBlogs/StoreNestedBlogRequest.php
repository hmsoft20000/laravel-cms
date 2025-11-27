<?php

namespace HMsoft\Cms\Http\Requests\NestedBlogs;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Content\Blog;
use HMsoft\Cms\Traits\Blogs\BlogValidationRules;

class StoreNestedBlogRequest extends MyRequest
{
    use BlogValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // You can add authorization logic here later based on the owner model.
        // For example: return $this->user()->can('createBlogsFor', $this->route('owner'));
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * We only need the core blog rules here. Nested relationships like
     * features or downloads are handled by their own dedicated endpoints.
     */
    public function rules(): array
    {
        // نحصل على اسم جدول المدونات للتحقق من الـ id
        $blogTable = resolve(Blog::class)->getTable();

        return [
            'blog_id'     => ["required", "integer", "exists:{$blogTable},id"],
            'sort_number' => ['nullable', 'integer'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }
}
