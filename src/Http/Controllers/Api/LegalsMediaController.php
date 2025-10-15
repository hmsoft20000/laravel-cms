<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Media\{UploadMediaRequest, UpdateMediaAllRequest};
use HMsoft\Cms\Models\Shared\Medium;
use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LegalsMediaController extends Controller
{
    protected string|null $type = null;
    public function __construct(
        private readonly MediaRepositoryInterface $repository,
    ) {
        $this->type = request()->route('type');
    }


    private function getLegalModel(): Model
    {
        $legalType = $this->type;

        if (!$legalType) {
            abort(404, 'Could not determine legal type from route name.');
        }

        $modelClass = config("cms.morph_map.{$legalType}");

        if (!$modelClass || !class_exists($modelClass)) {
            abort(404, "Model for '{$legalType}' not found in morph_map.");
        }

        // For legals, we typically have only one instance per type
        // You might need to adjust this logic based on your business requirements
        $modelInstance = $modelClass::where('type', $legalType)->first();

        if (!$modelInstance) {
            abort(404, "No {$legalType} record found.");
        }

        return $modelInstance;
    }

    /**
     * Display a listing of the resource for the legal type.
     */
    public function index(Request $request): JsonResponse
    {
        $owner = $this->getLegalModel();

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: new Medium(),
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
     * Store a newly created resource in storage and attach it to the legal model.
     */
    public function store(UploadMediaRequest $request): JsonResponse
    {
        $owner = $this->getLegalModel();

        $media = $this->repository->store($owner, $request->validated());

        return successResponse(
            message: translate('cms::messages.added_successfully'),
            data: $media,
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource and check if it belongs to the legal model.
     */
    public function show(Medium $medium): JsonResponse
    {
        $owner = $this->getLegalModel();

        // Check if the media belongs to the correct owner
        if ($medium->owner_type != $owner->getMorphClass() || $medium->owner_id != $owner->id) {
            abort(404);
        }

        return successResponse(
            message: translate('cms::messages.retrieved_successfully'),
            data: $this->repository->show($owner, $medium)
        );
    }

    /**
     * Update the specified resource in storage and check if it belongs to the legal model.
     */
    public function update(Request $request, Medium $medium): JsonResponse
    {
        $owner = $this->getLegalModel();

        // Check if the media belongs to the correct owner
        if ($medium->owner_type != $owner->getMorphClass() || $medium->owner_id != $owner->id) {
            abort(404);
        }

        $updatedMedia = $this->repository->update($owner, $medium, $request->all());

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: $updatedMedia
        );
    }

    /**
     * Remove the specified resource from storage and check if it belongs to the legal model.
     */
    public function destroy(Medium $medium): JsonResponse
    {
        $owner = $this->getLegalModel();

        // Check if the media belongs to the correct owner
        if ($medium->owner_type != $owner->getMorphClass() || $medium->owner_id != $owner->id) {
            abort(404);
        }

        $this->repository->delete($owner, $medium->id);

        return successResponse(
            message: translate('cms::messages.deleted_successfully')
        );
    }

    /**
     * Reorder media for the legal model.
     */
    public function reorder(UpdateMediaAllRequest $request): JsonResponse
    {
        $owner = $this->getLegalModel();

        $media = $this->repository->updateAll($owner, $request->validated()['media']);

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: $media
        );
    }

    /**
     * Update all media for the legal model.
     */
    public function updateAll(UpdateMediaAllRequest $request): JsonResponse
    {
        $owner = $this->getLegalModel();

        $media = $this->repository->updateAll($owner, $request->validated()['media']);

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: $media
        );
    }
}
