<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Contracts\AuthServiceInterface;
use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Blog\{StoreBlogRequest, UpdateBlogRequest, UpdateAllBlogRequest};
use HMsoft\Cms\Http\Resources\Api\BlogResource;
use HMsoft\Cms\Models\Content\Blog;
use HMsoft\Cms\Repositories\Contracts\BlogRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected AuthServiceInterface $authService;

    public function __construct(
        private readonly BlogRepositoryInterface $repo
    ) {
        $this->authService = app(AuthServiceInterface::class);
    }
    /**
     * Display a listing of the blogs.
     */
    public function index(): JsonResponse
    {
        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: resolve(Blog::class),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) {
                // if (!$this->authService->hasPermission('blogs.viewUnpublished')) {
                //     $query->where('t_main.is_active', true);
                // }

                $query->with([
                    'translations',
                    'media',
                    'keywords',
                    'categories.translations',
                    'features.translations',
                    'partners.translations',
                    'sponsors.translations',
                    'attributeValues.attribute.translations',
                    'attributeValues.attribute.options.translations',
                    'attributeValues.selectedOptions.option.translations',
                ]);
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
     * Store a newly created blog in storage.
     */
    public function store(StoreBlogRequest $request): JsonResponse
    {
        $blog = $this->repo->store($request->validated());
        return successResponse(
            message: translate('cms.messages.added_successfully'),
            data: resolve(BlogResource::class, ['resource' => $this->repo->show($blog)])->withFields(request()->get('fields')),
        );
    }

    /**
     * Display the specified blog.
     */
    public function show(Blog $blog): JsonResponse
    {
        $blog = $this->repo->show($blog);
        return successResponse(data: resolve(BlogResource::class, ['resource' => $blog])->withFields(request()->get('fields')));
    }

    /**
     * Update the specified blog in storage.
     */
    public function update(UpdateBlogRequest $request, Blog $blog): JsonResponse
    {
        $updatedBlog = $this->repo->update($blog, $request->validated());

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(BlogResource::class, ['resource' => $updatedBlog])->withFields(request()->get('fields'))
        );
    }

    public function updateAll(UpdateAllBlogRequest $request): JsonResponse
    {
        $updatedBlogs = [];
        foreach ($request->all() as $blogData) {
            if (isset($blogData['id'])) {
                $blog = Blog::findOrFail($blogData['id']);
                $updatedBlogs[] = $this->repo->update($blog, $blogData);
            }
        }

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: collect($updatedBlogs)->map(function ($item) {
                return resolve(BlogResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    /**
     * Remove the specified blog from storage.
     */
    public function destroy(Blog $blog): JsonResponse
    {
        $this->repo->delete($blog);
        return successResponse(
            message: translate('cms.messages.deleted_successfully'),
        );
    }

    public function attachDownloads(Request $request, $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);
        $this->repo->attachDownloads($blog, $request->input('download_item_ids', []));

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
        );
    }
}
