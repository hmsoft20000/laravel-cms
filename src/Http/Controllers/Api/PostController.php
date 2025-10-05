<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Contracts\AuthServiceInterface;
use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Posts\{StorePostRequest, UpdatePostRequest, UpdateAllPostRequest};
use HMsoft\Cms\Http\Resources\Api\PostResource;
use HMsoft\Cms\Models\Content\Post;
use HMsoft\Cms\Repositories\Contracts\PostRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
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
     * Display a listing of the posts.
     */
    public function index(): JsonResponse
    {
        // Check if user can view posts
        // $this->authorize('viewAny', Post::class);

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: new Post(),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) {
                $query->where('type', $this->type);

                // Filter unpublished posts based on user permissions
                // $user = User::currentOrGuest();
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
     * Store a newly created post in storage.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        // Check if user can create posts
        // $this->authorize('create', Post::class);

        $post = $this->repo->store($request->validated());
        return successResponse(
            message: translate('cms::messages.added_successfully'),
            data: (new PostResource($this->repo->show($post)))->withFields(request()->get('fields')),
        );
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): JsonResponse
    {
        // Check if user can view this specific post
        // $this->authorize('view', $post);

        $post = $this->repo->show($post);
        return successResponse(data: (new PostResource($post))->withFields(request()->get('fields')));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        // Check if user can update this specific post
        // $this->authorize('update', $post);

        $updatedPost = $this->repo->update($post, $request->validated());

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: (new PostResource($updatedPost))->withFields(request()->get('fields'))
        );
    }

    public function updateAll(UpdateAllPostRequest $request): JsonResponse
    {
        // $this->authorize('updateAny', Post::class);

        $updatedPosts = [];
        foreach ($request->all() as $postData) {
            if (isset($postData['id'])) {
                $post = Post::findOrFail($postData['id']);
                // $this->authorize('update', $post);
                $updatedPosts[] = $this->repo->update($post, $postData);
            }
        }

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: PostResource::collection($updatedPosts)
        );
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        // Check if user can delete this specific post
        // $this->authorize('delete', $post);

        $this->repo->delete($post); // Changed from destroy to delete to match repo
        return successResponse(
            message: translate('cms::messages.deleted_successfully'),
        );
    }
}
