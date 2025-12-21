<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Contracts\AuthServiceInterface;
use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Service\{StoreServiceRequest, UpdateServiceRequest, UpdateAllServiceRequest};
use HMsoft\Cms\Http\Resources\Api\ServiceResource;
use HMsoft\Cms\Models\Content\Service;
use HMsoft\Cms\Repositories\Contracts\ServiceRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected AuthServiceInterface $authService;

    public function __construct(
        private readonly ServiceRepositoryInterface $repo
    ) {
        $this->authService = app(AuthServiceInterface::class);
    }
    /**
     * Display a listing of the services.
     */
    public function index(): JsonResponse
    {
        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: resolve(Service::class),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) {
                // if (!$this->authService->hasPermission('services.viewUnpublished')) {
                //     $query->where('t_main.is_active', true);
                // }

                $query->with([
                    'translations',
                    'media',
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
            return resolve(ServiceResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = $this->repo->store($request->validated());
        return successResponse(
            message: translate('cms.messages.added_successfully'),
            data: resolve(ServiceResource::class, ['resource' => $this->repo->show($service)])->withFields(request()->get('fields')),
        );
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): JsonResponse
    {
        $service = $this->repo->show($service);
        return successResponse(data: resolve(ServiceResource::class, ['resource' => $service])->withFields(request()->get('fields')));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $updatedService = $this->repo->update($service, $request->validated());

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(ServiceResource::class, ['resource' => $updatedService])->withFields(request()->get('fields'))
        );
    }

    public function updateAll(UpdateAllServiceRequest $request): JsonResponse
    {
        $updatedServices = [];
        foreach ($request->all() as $serviceData) {
            if (isset($serviceData['id'])) {
                $service = Service::findOrFail($serviceData['id']);
                $updatedServices[] = $this->repo->update($service, $serviceData);
            }
        }

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: collect($updatedServices)->map(function ($item) {
                return resolve(ServiceResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service): JsonResponse
    {
        $this->repo->delete($service);
        return successResponse(
            message: translate('cms.messages.deleted_successfully'),
        );
    }

    public function attachDownloads(Request $request, $id): JsonResponse
    {
        $service = Service::findOrFail($id);
        $this->repo->attachDownloads($service, $request->input('download_item_ids', []));

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
        );
    }
}
