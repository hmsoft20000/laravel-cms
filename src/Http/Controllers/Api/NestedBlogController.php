<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\NestedBlogs\{StoreNestedBlogRequest, UpdateNestedBlogRequest, UpdateAllNestedBlogRequest};
use HMsoft\Cms\Http\Resources\Api\BlogResource;
use HMsoft\Cms\Models\Content\Blog;
use HMsoft\Cms\Repositories\Contracts\BlogRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NestedBlogController extends Controller
{
    public function __construct(
        private readonly BlogRepositoryInterface $repo
    ) {}

    /**
     * Display a listing of the blogs associated with the owner.
     * عرض المدونات المرتبطة بهذا العنصر فقط.
     */
    public function index(Request $request, Model $owner): JsonResponse
    {
        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: resolve(Blog::class),
            extraOperation: function ($query) use ($owner) {
                // [FIX]: نقوم بعمل Join يدوي مع الجدول الوسيط لكي تعمل الفلترة
                $query->join('bloggables', 'blogs.id', '=', 'bloggables.blog_id')
                    ->where('bloggables.bloggable_id', $owner->id)
                    ->where('bloggables.bloggable_type', $owner->getMorphClass())
                    ->select('blogs.*'); // نحدد أعمدة المدونات فقط لتجنب تضارب الـ ID

                // إضافة العلاقات المطلوبة
                $query->with(['translations']);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return resolve(BlogResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a new blog AND attach it to the owner.
     * إنشاء مدونة جديدة وربطها فوراً (Inline Create).
     */
    public function store(StoreNestedBlogRequest $request, Model $owner): JsonResponse
    {
        // 1. إنشاء المدونة (بدون owner_id لأننا ألغيناه)
        $validated = $request->validated();
        // $blog = $this->repo->store($validated);
        $blog = resolve(Blog::class)->find($validated['blog_id']);
        // 2. ربط المدونة بالعنصر (Attach)
        $owner->blogs()->attach($blog->id, [
            'sort_number' => 0,
            'is_active' => true
        ]);

        return successResponse(
            message: translate('cms.messages.added_successfully'),
            data: resolve(BlogResource::class, ['resource' => $blog])->withFields(request()->get('fields')),
        );
    }

    /**
     * Display the specified blog.
     * التحقق يتم عبر وجود الرابط في الجدول الوسيط.
     */
    public function show(Model $owner, Blog $blog): JsonResponse
    {
        // التحقق من أن هذه المدونة مرتبطة فعلاً بهذا العنصر
        $exists = $owner->blogs()->where('blogs.id', $blog->id)->exists();

        if (!$exists) {
            abort(404, 'This blog is not attached to this item.');
        }

        return successResponse(data: resolve(BlogResource::class, ['resource' => $this->repo->show($blog)])->withFields(request()->get('fields')));
    }

    /**
     * Update the specified blog.
     * تنبيه: هذا سيعدل المدونة الأصلية في المكتبة!
     */
    public function update(UpdateNestedBlogRequest $request, Model $owner, Blog $blog): JsonResponse
    {
        // التحقق من الارتباط
        $exists = $owner->blogs()->where('blogs.id', $blog->id)->exists();
        if (!$exists) {
            abort(404);
        }

        $updatedBlog = $this->repo->update($blog, $request->validated());

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(BlogResource::class, ['resource' => $updatedBlog])->withFields(request()->get('fields'))
        );
    }

    /**
     * Detach the blog from the owner.
     * هام: نقوم بفصل العلاقة (Detach) وليس الحذف (Delete).
     */
    public function destroy(Model $owner, Blog $blog): JsonResponse
    {
        // إلغاء الرابط بين العنصر والمدونة
        $detached = $owner->blogs()->detach($blog->id);

        if ($detached === 0) {
            abort(404, 'Blog not attached or already removed.');
        }

        return successResponse(
            message: translate('cms.messages.deleted_successfully'), // رسالة "تم الحذف" (من القائمة)
        );
    }

    /**
     * دالة إضافية لترتيب المدونات (اختياري)
     */
    public function reorder(Request $request, Model $owner)
    {
        // validation...
        foreach ($request->input('order') as $item) {
            $owner->blogs()->updateExistingPivot($item['id'], ['sort_number' => $item['sort']]);
        }
        return successResponse(message: 'Reordered');
    }
}
