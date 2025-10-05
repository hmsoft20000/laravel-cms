<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Features\{StoreFeatureRequest, UpdateAllFeatureRequest, UpdateFeatureRequest};
use HMsoft\Cms\Http\Resources\Api\FeatureResource;
use HMsoft\Cms\Models\Shared\Feature;
use HMsoft\Cms\Repositories\Contracts\FeatureRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeatureController extends Controller
{


    public function __construct(
        private readonly FeatureRepositoryInterface $repository
    ) {}


    /**
     * Display a listing of the resource, scoped by the owner type from the route.
     * @param Request $request
     * @param Model $owner The magic happens here. This will be an instance of Post OR Product.
     * @return JsonResponse
     */
    public function index(Request $request, Model $owner): JsonResponse
    {
        // $this->authorize('viewAny', [Feature::class, $owner]);

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: new Feature(),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($owner) {
                $query->where('owner_type', $owner->getMorphClass());
                $query->where('owner_id', $owner->id);
                $query->with([
                    'translations',
                ]);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return new FeatureResource($item);
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created resource in storage and attach it to the owner model.
     * @param StoreFeatureRequest $request
     * @param Model $owner
     * @return JsonResponse
     */
    public function store(StoreFeatureRequest $request, Model $owner): JsonResponse
    {
        // $this->authorize('create', Feature::class);
        $feature = $this->repository->store($request->validated());
        return successResponse(
            message: translate('cms::messages.added_successfully'),
            data: new FeatureResource($feature),
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource and check if it belongs to the owner model.
     * @param Model $owner
     * @param Feature $feature
     * @return JsonResponse
     */
    public function show(Model $owner, Feature $feature): JsonResponse
    {
        // $this->authorize('view', $feature);

        // Optional: Add a check to ensure the plan belongs to the correct type
        if ($feature->owner_type != $owner->getMorphClass() || $feature->owner_id != $owner->id) {
            abort(404);
        }
        return successResponse(data: new FeatureResource($this->repository->show($feature)));
    }

    /**
     * Update the specified resource in storage and check if it belongs to the owner model.
     *
     * @param UpdateFeatureRequest $request
     * @param Model $owner
     * @param Feature $feature
     * @return JsonResponse
     */
    public function update(UpdateFeatureRequest $request, Model $owner, Feature $feature): JsonResponse
    {
        // $this->authorize('update', $feature);

        // Optional: Add a check to ensure the feature belongs to the correct type
        if ($feature->owner_type != $owner->getMorphClass() || $feature->owner_id != $owner->id) {
            abort(404);
        }
        $updatedFeature = $this->repository->update($feature, $request->validated());
        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: new FeatureResource($updatedFeature)
        );
    }

    /**
     * Remove the specified resource from storage and check if it belongs to the owner model.
     *
     * @param Model $owner
     * @param Feature $feature
     * @return JsonResponse
     */
    public function destroy(Model $owner, Feature $feature): JsonResponse
    {
        // $this->authorize('delete', $feature);

        // Optional: Add a check to ensure the feature belongs to the correct type
        if ($feature->owner_type != $owner->getMorphClass() || $feature->owner_id != $owner->id) {
            abort(404);
        }
        $this->repository->delete($feature);
        return successResponse(message: translate('cms::messages.deleted_successfully'));
    }

    /**
     * Update all the features for the owner model.
     *
     * @param UpdateAllFeatureRequest $request
     * @param Model $owner
     * @return JsonResponse
     */
    public function updateAll(UpdateAllFeatureRequest $request, Model $owner): JsonResponse
    {
        // $this->authorize('bulkUpdate', Feature::class);

        $updatedFeatures = [];
        foreach ($request->all() as $featureData) {
            if (isset($featureData['id'])) {
                $feature = Feature::findOrFail($featureData['id']);
                if ($feature->owner_type != $owner->getMorphClass() || $feature->owner_id != $owner->id) {
                    abort(404);
                }
                $updatedFeatures[] = $this->repository->update($feature, $featureData);
            }
        }

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: FeatureResource::collection($updatedFeatures)
        );
    }

    /**
     * Update the image of the specified resource and check if it belongs to the owner model.
     *
     * @param Request $request
     * @param Model $owner
     * @param Feature $feature
     * @return JsonResponse
     */
    public function updateImage(Request $request, Model $owner, Feature $feature): JsonResponse
    {
        // $this->authorize('manageMedia', $feature);

        if ($feature->owner_type != $owner->getMorphClass() || $feature->owner_id != $owner->id) {
            abort(404);
        }

        $validated = $request->validate([
            'image' => ['sometimes', 'image', 'max:2048'],
            'delete_image' => ['sometimes'],
        ]);

        $updatedFeature = $this->repository->update($feature, $validated);

        return successResponse(
            message: translate('cms::messages.image_updated_successfully'),
            data: new FeatureResource($updatedFeature)
        );
    }
}
