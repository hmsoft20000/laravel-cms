<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Contracts\AuthServiceInterface;
use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\NestedPosts\{StoreNestedPostRequest, UpdateNestedPostRequest, UpdateAllNestedPostRequest};
use HMsoft\Cms\Http\Resources\Api\PostResource;
use HMsoft\Cms\Models\Content\Post;
use HMsoft\Cms\Repositories\Contracts\PostRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NestedPostController extends Controller
{
    protected ?string $type;

    protected AuthServiceInterface $authService;

    public function __construct(
        private readonly PostRepositoryInterface $repo
    ) {
        $this->type = request()->route('type');
        $this->authService = app(AuthServiceInterface::class);
    }

    /**
     * Display a listing of the posts scoped by the owner type from the route.
     * @param Request $request
     * @param Model $owner The magic happens here. This will be an instance of Product OR Portfolio OR Item.
     * @return JsonResponse
     */
    public function index(Request $request, Model $owner): JsonResponse
    {
        // Check if user can view posts
        // $this->authorize('viewAny', [Post::class, $owner]);

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: new Post(),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($owner) {
                $query->where('owner_type', $owner->getMorphClass());
                $query->where('owner_id', $owner->id);
                $query->where('type', $this->type);

                // Filter unpublished posts based on user permissions
                if (!$this->authService->hasPermission('posts.viewUnpublished')) {
                    $query->where('is_active', true);
                }

                $query->with([
                    'translations',
                    'media', // Formerly 'images'
                    'keywords',
                    'categories.translations',
                    'features.translations',
                    'downloads.translations',
                    'partners.translations',
                    'sponsors.translations',
                    'attributeValues.attribute.translations',
                    'attributeValues.attribute.options.translations',
                    'attributeValues.selectedOptions.option.translations',
                ]);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return (new PostResource($item))->withFields(request()->get('fields'));
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created post in storage and attach it to the owner model.
     * @param StoreNestedPostRequest $request
     * @param Model $owner
     * @return JsonResponse
     */
    public function store(StoreNestedPostRequest $request, Model $owner): JsonResponse
    {
        // Check if user can create posts
        // $this->authorize('create', [Post::class, $owner]);

        $post = $this->repo->store($request->validated());
        return successResponse(
            message: translate('cms::messages.added_successfully'),
            data: (new PostResource($this->repo->show($post)))->withFields(request()->get('fields')),
        );
    }

    /**
     * Display the specified post and check if it belongs to the owner model.
     * @param Model $owner
     * @param Post $post
     * @return JsonResponse
     */
    public function show(Model $owner, Post $post): JsonResponse
    {
        // Check if user can view this specific post
        // $this->authorize('view', $post);

        // Optional: Add a check to ensure the post belongs to the correct owner
        if ($post->owner_type != $owner->getMorphClass() || $post->owner_id != $owner->id) {
            abort(404);
        }

        $post = $this->repo->show($post);
        return successResponse(data: (new PostResource($post))->withFields(request()->get('fields')));
    }

    /**
     * Update the specified post in storage and check if it belongs to the owner model.
     *
     * @param UpdateNestedPostRequest $request
     * @param Model $owner
     * @param Post $post
     * @return JsonResponse
     */
    public function update(UpdateNestedPostRequest $request, Model $owner, Post $post): JsonResponse
    {
        // Check if user can update this specific post
        // $this->authorize('update', $post);

        // Optional: Add a check to ensure the post belongs to the correct owner
        if ($post->owner_type != $owner->getMorphClass() || $post->owner_id != $owner->id) {
            abort(404);
        }

        $updatedPost = $this->repo->update($post, $request->validated());

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: (new PostResource($updatedPost))->withFields(request()->get('fields'))
        );
    }

    /**
     * Remove the specified post from storage and check if it belongs to the owner model.
     *
     * @param Model $owner
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Model $owner, Post $post): JsonResponse
    {
        // Check if user can delete this specific post
        // $this->authorize('delete', $post);

        // Optional: Add a check to ensure the post belongs to the correct owner
        if ($post->owner_type != $owner->getMorphClass() || $post->owner_id != $owner->id) {
            abort(404);
        }

        $this->repo->delete($post);
        return successResponse(
            message: translate('cms::messages.deleted_successfully'),
        );
    }

    /**
     * Update all the posts for the owner model.
     *
     * @param UpdateAllNestedPostRequest $request
     * @param Model $owner
     * @return JsonResponse
     */
    public function updateAll(UpdateAllNestedPostRequest $request, Model $owner): JsonResponse
    {
        // $this->authorize('bulkUpdate', [Post::class, $owner]);

        $updatedPosts = [];
        foreach ($request->all() as $postData) {
            if (isset($postData['id'])) {
                $post = Post::findOrFail($postData['id']);
                
                // Check if the post belongs to the correct owner
                if ($post->owner_type != $owner->getMorphClass() || $post->owner_id != $owner->id) {
                    abort(404);
                }
                
                $updatedPosts[] = $this->repo->update($post, $postData);
            }
        }

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: PostResource::collection($updatedPosts)
        );
    }
}