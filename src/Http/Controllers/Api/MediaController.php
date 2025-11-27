<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Media\BulkUploadMediaRequest;
use HMsoft\Cms\Http\Requests\Media\{UploadMediaRequest, UpdateMediaAllRequest};
use HMsoft\Cms\Models\Shared\Medium;
use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use HMsoft\Cms\Traits\General\ResolvesRouteOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MediaController extends Controller
{

    use ResolvesRouteOwner;


    public function __construct(
        private readonly MediaRepositoryInterface $repository,
    ) {}

    /**
     * Display a listing of the resource, scoped by the owner type from the route.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        /** @var Model $owner */
        $owner = $this->resolveOwner($request);

        // $this->authorize('viewAny', [Medium::class, $owner]);

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: resolve(Medium::class),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($owner) {
                $query->where('owner_type', $owner->getMorphClass());
                $query->where('owner_id', $owner->id);
            },
        );

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created resource in storage and attach it to the owner model.
     * @param UploadMediaRequest $request
     * @return JsonResponse
     */
    public function store(UploadMediaRequest $request): JsonResponse
    {
        // $this->authorize('create', Medium::class);

        /** @var Model $owner */
        $owner = $this->resolveOwner($request);

        $validated = $request->validated();
        $ownerData = [
            'owner_id' => $owner->id,
            'owner_type' => $owner->getMorphClass(),
        ];
        $validated = array_merge($validated, $ownerData);
        $media = $this->repository->store($owner, $validated);

        return successResponse(
            message: translate('cms.messages.added_successfully'),
            data: $media,
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Store a newly created resource in storage and attach it to the owner model.
     * @param BulkUploadMediaRequest $request
     * @return JsonResponse
     */
    public function bulkUpload(BulkUploadMediaRequest $request): JsonResponse
    {

        // $this->authorize('create', Medium::class);

        /** @var Model $owner */
        $owner = $this->resolveOwner($request);

        $validated = $request->validated();

        $media = $this->repository->store($owner, $validated);

        return successResponse(
            message: translate('cms.messages.added_successfully'),
            data: $media,
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource and check if it belongs to the owner model.
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        // $this->authorize('view', $medium);

        /** @var Model $owner */
        $owner = $this->resolveOwner($request);
        $medium = $this->resolveRouteParameter($request, 'medium');

        // Check if the media belongs to the correct owner
        if ($medium->owner_type != $owner->getMorphClass() || $medium->owner_id != $owner->id) {
            abort(404);
        }

        return successResponse(
            message: translate('cms.messages.retrieved_successfully'),
            data: $this->repository->show($owner, $medium)
        );
    }

    /**
     * Update the specified resource in storage and check if it belongs to the owner model.
     *
     * @param Request $request
     * @param Medium $medium
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        // $this->authorize('update', $medium);

        /** @var Model $owner */
        $owner = $this->resolveOwner($request);
        $medium = $this->resolveRouteParameter($request, 'medium');

        // Check if the media belongs to the correct owner
        if ($medium->owner_type != $owner->getMorphClass() || $medium->owner_id != $owner->id) {
            abort(404);
        }

        $updatedMedia = $this->repository->update($owner, $medium, $request->all());

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: $updatedMedia
        );
    }

    /**
     * Remove the specified resource from storage and check if it belongs to the owner model.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        // $this->authorize('delete', $medium);

        /** @var Model $owner */
        $owner = $this->resolveOwner($request);
        $medium = $this->resolveRouteParameter($request, 'medium');

        // Check if the media belongs to the correct owner
        if ($medium->owner_type != $owner->getMorphClass() || $medium->owner_id != $owner->id) {
            abort(404);
        }

        $this->repository->delete($owner, $medium->id);

        return successResponse(
            message: translate('cms.messages.deleted_successfully')
        );
    }

    /**
     * Reorder media for the owner model.
     *
     * @param UpdateMediaAllRequest $request
     * @return JsonResponse
     */
    public function reorder(UpdateMediaAllRequest $request): JsonResponse
    {
        // $this->authorize('manageMedia', $owner);

        /** @var Model $owner */
        $owner = $this->resolveOwner($request);

        $media = $this->repository->updateAll($owner, $request->validated()['media']);

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: $media
        );
    }

    /**
     * Update all media for the owner model.
     *
     * @param UpdateMediaAllRequest $request
     * @return JsonResponse
     */
    public function updateAll(UpdateMediaAllRequest $request): JsonResponse
    {
        // $this->authorize('manageMedia', $owner);

        /** @var Model $owner */
        $owner = $this->resolveOwner($request);

        $media = $this->repository->updateAll($owner, $request->validated()['media']);

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: $media
        );
    }
}
