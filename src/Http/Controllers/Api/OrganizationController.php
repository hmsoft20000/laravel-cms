<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Organizations\{Store, Update, Delete, UpdateAll};
use HMsoft\Cms\Http\Resources\Api\OrganizationResource;
use HMsoft\Cms\Models\Organizations\Organization;
use HMsoft\Cms\Repositories\Contracts\OrganizationRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{

    protected string|null $type;

    public function __construct(
        private readonly OrganizationRepositoryInterface  $repo,
    ) {}

    public function index(Request $request)
    {
        // $this->authorize('viewAny', Organization::class);

        $type = request()->route('type');

        $result =  AutoFilterAndSortService::dynamicSearchFromRequest(
            model: Organization::class,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) use ($type) {
                $query->with(['translations', 'image']);
                if (isset($type)) {
                    $query->ofType($type);
                }
            },
        );

        $result['data'] =  collect($result['data'])->map(function ($item) {
            return resolve(OrganizationResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    public function show(Organization $organization)
    {
        // $this->authorize('view', $organization);

        $organization->load(['translations', 'image']);
        return  successResponse(
            data: resolve(OrganizationResource::class, ['resource' => $organization])->withFields(request()->get('fields'))
        );
    }

    public function store(Store $request)
    {
        // $this->authorize('create', Organization::class);

        $validated = $request->validated();
        $organization = $this->repo->store($validated);
        $organization->load(['translations', 'image']);
        return  successResponse(
            message: translate('cms.messages.added_successfully'),
            data: resolve(OrganizationResource::class, ['resource' => $organization])->withFields(request()->get('fields'))
        );
    }

    public function update(Update $request, Organization $organization)
    {
        // $this->authorize('update', $organization);

        $validated = $request->validated();
        $organization = $this->repo->update($organization, $validated);
        $organization->load(['translations', 'image']);
        return  successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(OrganizationResource::class, ['resource' => $organization])->withFields(request()->get('fields'))
        );
    }


    public function updateAll(UpdateAll $request)
    {
        // $this->authorize('bulkUpdate', Organization::class);

        $updatedModel = [];
        foreach ($request->all() as $featureData) {
            if (isset($featureData['id'])) {
                $download = Organization::findOrFail($featureData['id']);
                $updatedModel[] = $this->repo->update($download, $featureData);
            }
        }

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: collect($updatedModel)->map(function ($item) {
                return resolve(OrganizationResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    public function updateImage(Request $request, Organization $organization): JsonResponse
    {
        // $this->authorize('manageImages', $organization);

        $validated = $request->validate([
            'image' => ['required'],
        ]);

        $updatedOrganization = $this->repo->update($organization, $validated);
        $updatedOrganization->load(['image']);

        return successResponse(
            message: translate('cms.messages.image_updated_successfully'),
            data: resolve(OrganizationResource::class, ['resource' => $updatedOrganization])->withFields(request()->get('fields')),
        );
    }

    public function destroy(Delete $request, Organization $organization)
    {
        // $this->authorize('delete', $organization);

        $this->repo->destroy($organization);
        return  successResponse(
            message: translate('cms.messages.deleted_successfully'),
        );
    }
}
