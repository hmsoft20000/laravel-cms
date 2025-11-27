<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Sector\{Store, Update, Delete, UpdateAll};
use HMsoft\Cms\Http\Resources\Api\SectorResource;
use HMsoft\Cms\Models\Sector\Sector;
use HMsoft\Cms\Repositories\Contracts\SectorRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectorController extends Controller
{
    public function __construct(
        private readonly SectorRepositoryInterface  $repo,
    ) {}

    public function index(Request $request)
    {
        // $this->authorize('viewAny', Sector::class);

        $result =  AutoFilterAndSortService::dynamicSearchFromRequest(
            model: Sector::class,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations', 'image']);
            },
        );

        $result['data'] =  collect($result['data'])->map(function ($item) {
            return resolve(SectorResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    public function show(Sector $sector)
    {
        // $this->authorize('view', $sector);

        $sector->load(['translations', 'image']);
        return  successResponse(
            data: resolve(SectorResource::class, ['resource' => $sector])->withFields(request()->get('fields'))
        );
    }

    public function store(Store $request)
    {
        // $this->authorize('create', Sector::class);

        $validated = $request->validated();
        $sector = $this->repo->store($validated);
        $sector->load(['translations', 'image']);
        return  successResponse(
            message: translate('cms.messages.added_successfully'),
            data: resolve(SectorResource::class, ['resource' => $sector])->withFields(request()->get('fields'))
        );
    }

    public function update(Update $request, Sector $sector)
    {
        // $this->authorize('update', $sector);

        $validated = $request->validated();
        $sector = $this->repo->update($sector, $validated);
        $sector->load(['translations', 'image']);
        return  successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(SectorResource::class, ['resource' => $sector])->withFields(request()->get('fields'))
        );
    }

    public function updateAll(UpdateAll $request)
    {
        // $this->authorize('updateAny', Sector::class);

        $updatedSectors = [];
        foreach ($request->all() as $sectorData) {
            if (isset($sectorData['id'])) {
                $sector = Sector::findOrFail($sectorData['id']);
                // $this->authorize('update', $sector);
                $updatedSectors[] = $this->repo->update($sector, $sectorData);
            }
        }

        // Load translations and image for all updated sectors
        $updatedSectors = collect($updatedSectors)->map(function ($sector) {
            $sector->load(['translations', 'image']);
            return $sector;
        });

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: collect($updatedSectors)->map(function ($item) {
                return resolve(SectorResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    public function updateImage(Request $request, Sector $sector): JsonResponse
    {
        // $this->authorize('manageImages', $sector);

        $validated = $request->validate([
            'image' => ['required'],
        ]);

        $updatedSector = $this->repo->update($sector, $validated);
        $updatedSector->load(['image']);

        return successResponse(
            message: translate('cms.messages.image_updated_successfully'),
            data: resolve(SectorResource::class, ['resource' => $updatedSector])->withFields(request()->get('fields')),
        );
    }

    public function destroy(Delete $request, Sector $sector)
    {
        // $this->authorize('delete', $sector);

        $this->repo->destroy($sector);
        return  successResponse(
            message: translate('cms.messages.deleted_successfully'),
        );
    }
}
