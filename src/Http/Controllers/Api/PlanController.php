<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Plan\{StorePlanRequest, UpdatePlanRequest, UpdateAllPlanRequest};
use HMsoft\Cms\Http\Resources\Api\PlanResource;
use HMsoft\Cms\Models\Shared\Plan;
use HMsoft\Cms\Repositories\Contracts\PlanRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;

class PlanController extends Controller
{

    public function __construct(private readonly PlanRepositoryInterface $repository) {}

    /**
     * @param Request $request
     * @param Model $owner The magic happens here. This will be an instance of Post OR Product.
     * @return JsonResponse
     */
    public function index(Request $request, Model $owner): JsonResponse
    {
        // $this->authorize('viewAny', [Plan::class, $owner]);

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: new Plan(),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($owner) {
                $query->where('owner_type', $owner->getMorphClass());
                $query->where('owner_id', $owner->id);
                $query->with([
                    'translations',
                    'features.translations'
                ]);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return new PlanResource($item);
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created resource in storage and attach it to the owner model.
     *
     * @param StorePlanRequest $request
     * @param Model $owner
     * @return JsonResponse
     */
    public function store(StorePlanRequest $request, Model $owner): JsonResponse
    {
        // $this->authorize('create', Plan::class);
        $validated = $request->validated();
        $ownerData = [
            'owner_id' => $owner->id,
            'owner_type' => $owner->getMorphClass(),
        ];
        $validated = array_merge($validated, $ownerData);
        $plan = $this->repository->store($validated);
        return successResponse(
            message: translate('cms::messages.added_successfully'),
            data: new PlanResource($plan),
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource and check if it belongs to the owner model.
     *
     * @param Model $owner
     * @param Plan $plan
     * @return JsonResponse
     */
    public function show(Model $owner, Plan $plan): JsonResponse
    {
        // $this->authorize('view', $plan);

        // Optional: Add a check to ensure the plan belongs to the correct type
        if ($plan->owner_type != $owner->getMorphClass() || $plan->owner_id != $owner->id) {
            abort(404);
        }
        return successResponse(data: new PlanResource($this->repository->show($plan)));
    }


    /**
     * Update the specified resource in storage and check if it belongs to the owner model.
     *
     * @param UpdatePlanRequest $request
     * @param Model $owner
     * @param Plan $plan
     * @return JsonResponse
     */
    public function update(UpdatePlanRequest $request, Model $owner, Plan $plan): JsonResponse
    {
        // $this->authorize('update', $plan);

        // Optional: Add a check to ensure the plan belongs to the correct type
        if ($plan->owner_type != $owner->getMorphClass() || $plan->owner_id != $owner->id) {
            abort(404);
        }
        $updatedPlan = $this->repository->update($plan, $request->validated());
        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: new PlanResource($updatedPlan)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Model $owner
     * @param Plan $plan
     * @return JsonResponse
     */
    public function destroy(Model $owner, Plan $plan): JsonResponse
    {
        // $this->authorize('delete', $plan);

        // Optional: Add a check to ensure the plan belongs to the correct type
        if ($plan->owner_type != $owner->getMorphClass() || $plan->owner_id != $owner->id) {
            abort(404);
        }
        $this->repository->delete($plan);
        return successResponse(message: translate('cms::messages.deleted_successfully'));
    }

    /**
     * Update all the plans for the owner model.
     *
     * @param UpdateAllPlanRequest $request
     * @param Model $owner
     * @return JsonResponse
     */
    public function updateAll(UpdateAllPlanRequest $request, Model $owner): JsonResponse
    {
        // $this->authorize('bulkUpdate', Plan::class);

        $updatedPlans = [];
        foreach ($request->all() as $planData) {
            if (isset($planData['id'])) {
                $plan = Plan::findOrFail($planData['id']);
                if ($plan->owner_type != $owner->getMorphClass() || $plan->owner_id != $owner->id) {
                    abort(404);
                }
                $updatedPlans[] = $this->repository->update($plan, $planData);
            }
        }

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: PlanResource::collection($updatedPlans)
        );
    }
}
