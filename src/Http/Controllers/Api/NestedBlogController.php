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
use Illuminate\Http\Response;
// Assuming you will create these Form Requests
// use HMsoft\Cms\Http\Requests\NestedBlogs\StoreNestedBlogRequest;
// use HMsoft\Cms\Http\Requests\NestedBlogs\UpdateNestedBlogRequest;

class NestedBlogController extends Controller
{
    public function __construct(
        private readonly BlogRepositoryInterface $repo
    ) {}

    /**
     * Display a listing of the blogs, scoped by the owner model.
     */
    public function index(Request $request, Model $owner): JsonResponse
    {

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: new Blog(),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($owner) {
                $query->where('owner_type', $owner->getMorphClass());
                $query->where('owner_id', $owner->id);
                $query->with([
                    'translations',
                ]);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return new BlogResource($item);
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created blog and attach it to the owner model.
     */
    public function store(StoreNestedBlogRequest $request, Model $owner): JsonResponse // Replace with StoreNestedBlogRequest
    {
        $validated = $request->validated();
        $ownerData = [
            'owner_id' => $owner->id,
            'owner_type' => $owner->getMorphClass(),
        ];
        $validated = array_merge($validated, $ownerData);
        $blog = $this->repo->store($validated);

        return successResponse(
            message: translate('cms::messages.added_successfully'),
            data: (new BlogResource($blog))->withFields(request()->get('fields')),
        );
    }

    /**
     * Display the specified blog.
     * Scoped binding will automatically ensure the blog belongs to the owner.
     */
    public function show(Model $owner, Blog $blog): JsonResponse
    {
        if ($blog->owner_type != $owner->getMorphClass() || $blog->owner_id != $owner->id) {
            abort(404);
        }
        return successResponse(data: (new BlogResource($this->repo->show($blog)))->withFields(request()->get('fields')));
    }

    /**
     * Update the specified blog.
     * Scoped binding will automatically ensure the blog belongs to the owner.
     */
    public function update(UpdateNestedBlogRequest $request, Model $owner, Blog $blog): JsonResponse // Replace with UpdateNestedBlogRequest
    {

        if ($blog->owner_type != $owner->getMorphClass() || $blog->owner_id != $owner->id) {
            abort(404);
        }

        $updatedBlog = $this->repo->update($blog, $request->validated());

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: (new BlogResource($updatedBlog))->withFields(request()->get('fields'))
        );
    }

    public function updateAll(UpdateAllNestedBlogRequest $request, Model $owner): JsonResponse
    {
        $updatedBlogs = [];
        foreach ($request->all() as $blogData) {
            if (isset($blogData['id'])) {
                $blog = Blog::findOrFail($blogData['id']);
                if ($blog->owner_type != $owner->getMorphClass() || $blog->owner_id != $owner->id) {
                    abort(404);
                }
                $updatedBlogs[] = $this->repo->update($blog, $blogData);
            }
        }

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: BlogResource::collection($updatedBlogs)
        );
    }

    /**
     * Remove the specified blog from storage.
     * Scoped binding will automatically ensure the blog belongs to the owner.
     */
    public function destroy(Model $owner, Blog $blog): JsonResponse
    {
        if ($blog->owner_type != $owner->getMorphClass() || $blog->owner_id != $owner->id) {
            abort(404);
        }
        $this->repo->delete($blog);

        return successResponse(
            message: translate('cms::messages.deleted_successfully'),
        );
    }
}
