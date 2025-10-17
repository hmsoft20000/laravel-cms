<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\NestedServices\{StoreNestedServiceRequest, UpdateNestedServiceRequest, UpdateAllNestedServiceRequest};
use HMsoft\Cms\Http\Resources\Api\ServiceResource;
use HMsoft\Cms\Models\Content\Service;
use HMsoft\Cms\Repositories\Contracts\ServiceRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NestedServiceController extends Controller
{
    public function __construct(
        private readonly ServiceRepositoryInterface $repo
    ) {}

    /**
     * Display a listing of the services, scoped by the owner model.
     */
    public function index(Request $request, Model $owner): JsonResponse
    {

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: resolve(Service::class),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($owner) {
                $query->where('owner_type', $owner->getMorphClass());
                $query->where('owner_id', $owner->id);
                $query->with([
                    'translations',
                ]);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return resolve(ServiceResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created service and attach it to the owner model.
     */
    public function store(StoreNestedServiceRequest $request, Model $owner): JsonResponse // Replace with StoreNestedServiceRequest
    {
        $validated = $request->validated();
        $ownerData = [
            'owner_id' => $owner->id,
            'owner_type' => $owner->getMorphClass(),
        ];
        $validated = array_merge($validated, $ownerData);
        $service = $this->repo->store($validated);

        return successResponse(
            message: translate('cms::messages.added_successfully'),
            data: resolve(ServiceResource::class, ['resource' => $service])->withFields(request()->get('fields')),
        );
    }

    /**
     * Display the specified service.
     * Scoped binding will automatically ensure the service belongs to the owner.
     */
    public function show(Model $owner, Service $service): JsonResponse
    {
        if ($service->owner_type != $owner->getMorphClass() || $service->owner_id != $owner->id) {
            abort(404);
        }
        return successResponse(data: resolve(ServiceResource::class, ['resource' => $this->repo->show($service)])->withFields(request()->get('fields')));
    }

    /**
     * Update the specified service.
     * Scoped binding will automatically ensure the service belongs to the owner.
     */
    public function update(UpdateNestedServiceRequest $request, Model $owner, Service $service): JsonResponse // Replace with UpdateNestedServiceRequest
    {

        if ($service->owner_type != $owner->getMorphClass() || $service->owner_id != $owner->id) {
            abort(404);
        }

        $updatedService = $this->repo->update($service, $request->validated());

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: resolve(ServiceResource::class, ['resource' => $updatedService])->withFields(request()->get('fields'))
        );
    }

    public function updateAll(UpdateAllNestedServiceRequest $request, Model $owner): JsonResponse
    {
        $updatedServices = [];
        foreach ($request->all() as $serviceData) {
            if (isset($serviceData['id'])) {
                $service = Service::findOrFail($serviceData['id']);
                if ($service->owner_type != $owner->getMorphClass() || $service->owner_id != $owner->id) {
                    abort(404);
                }
                $updatedServices[] = $this->repo->update($service, $serviceData);
            }
        }

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: collect($updatedServices)->map(function ($item) {
                return resolve(ServiceResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    /**
     * Remove the specified service from storage.
     * Scoped binding will automatically ensure the service belongs to the owner.
     */
    public function destroy(Model $owner, Service $service): JsonResponse
    {
        if ($service->owner_type != $owner->getMorphClass() || $service->owner_id != $owner->id) {
            abort(404);
        }
        $this->repo->delete($service);

        return successResponse(
            message: translate('cms::messages.deleted_successfully'),
        );
    }
}
