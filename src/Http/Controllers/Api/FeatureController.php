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
use HMsoft\Cms\Traits\General\ResolvesRouteOwner;

class FeatureController extends Controller
{

    use ResolvesRouteOwner;

    public function __construct(
        private readonly FeatureRepositoryInterface $repository
    ) {}


    /**
     * Display a listing of the resource, scoped by the owner type from the route.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        // $this->authorize('viewAny', [Feature::class, $owner]);

        /** @var Model $owner */
        $owner = $this->resolveOwner($request);

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: resolve(Feature::class),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($owner) {
                $query->where('owner_type', $owner->getMorphClass());
                $query->where('owner_id', $owner->id);
                $query->with([
                    'translations',
                    'image',
                    'media',
                ]);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return resolve(FeatureResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created resource in storage and attach it to the owner model.
     * @param StoreFeatureRequest $request
     * @return JsonResponse
     */
    public function store(StoreFeatureRequest $request): JsonResponse
    {
        // $this->authorize('create', Feature::class);


        /** @var Model $owner */
        $owner = $this->resolveOwner($request);

        $validated = $request->validated();
        $ownerData = [
            'owner_id' => $owner->id,
            'owner_type' => $owner->getMorphClass(),
        ];
        $validated = array_merge($validated, $ownerData);
        $feature = $this->repository->store($validated);
        return successResponse(
            message: translate('cms.messages.added_successfully'),
            data: resolve(FeatureResource::class, ['resource' => $feature])->withFields(request()->get('fields')),
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

        // $this->authorize('view', $feature);


        $owner = $this->resolveOwner($request);

        /** @var Feature $feature */
        $feature = $this->resolveRouteParameter($request, 'feature');

        // Optional: Add a check to ensure the plan belongs to the correct type
        if ($feature->owner_type != $owner->getMorphClass() || $feature->owner_id != $owner->id) {
            abort(404);
        }
        return successResponse(data: resolve(FeatureResource::class, ['resource' => $this->repository->show($feature)])->withFields(request()->get('fields')));
    }

    /**
     * Update the specified resource in storage and check if it belongs to the owner model.
     *
     * @param UpdateFeatureRequest $request
     * @return JsonResponse
     */
    public function update(UpdateFeatureRequest $request): JsonResponse
    {
        // $this->authorize('update', $feature);

        $owner = $this->resolveOwner($request);

        /** @var Feature $feature */
        $feature = $this->resolveRouteParameter($request, 'feature');

        // Optional: Add a check to ensure the feature belongs to the correct type
        if ($feature->owner_type != $owner->getMorphClass() || $feature->owner_id != $owner->id) {
            abort(404);
        }
        $updatedFeature = $this->repository->update($feature, $request->validated());
        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(FeatureResource::class, ['resource' => $updatedFeature])->withFields(request()->get('fields'))
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

        /** @var Model $owner */
        $owner = $this->resolveOwner($request);

        /** @var Feature $feature */
        $feature = $this->resolveRouteParameter($request, 'feature');



        // $this->authorize('delete', $feature);

        // Optional: Add a check to ensure the feature belongs to the correct type
        if ($feature->owner_type != $owner->getMorphClass() || $feature->owner_id != $owner->id) {
            abort(404);
        }
        $this->repository->delete($feature);
        return successResponse(message: translate('cms.messages.deleted_successfully'));
    }

    /**
     * Update all the features for the owner model.
     *
     * @param UpdateAllFeatureRequest $request
     * @return JsonResponse
     */
    public function updateAll(UpdateAllFeatureRequest $request): JsonResponse
    {

        $owner = $this->resolveOwner($request);

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
            message: translate('cms.messages.updated_successfully'),
            data: collect($updatedFeatures)->map(function ($item) {
                return resolve(FeatureResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    /**
     * Update the image of the specified resource and check if it belongs to the owner model.
     *
     * @param Request $request
     * @param Feature $feature
     * @return JsonResponse
     */
    public function updateImage(Request $request): JsonResponse
    {

        $owner = $this->resolveOwner($request);

        /** @var Feature $feature */
        $feature = $this->resolveRouteParameter($request, 'feature');


        // $this->authorize('manageMedia', $feature);

        if ($feature->owner_type != $owner->getMorphClass() || $feature->owner_id != $owner->id) {
            abort(404);
        }
        $validated = $request->validate([
            'image' => ['sometimes'],
            'delete_image' => ['sometimes'],
        ]);

        $updatedFeature = $this->repository->update($feature, $validated);

        return successResponse(
            message: translate('cms.messages.image_updated_successfully'),
            data: resolve(FeatureResource::class, ['resource' => $updatedFeature])->withFields(request()->get('fields')),
        );
    }
}
