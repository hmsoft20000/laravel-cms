<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\OurValue\{Store, Update, UpdateAll};
use HMsoft\Cms\Http\Resources\Api\OurValueResource;
use HMsoft\Cms\Models\OurValue\OurValue;
use HMsoft\Cms\Repositories\Contracts\OurValueRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OurValueController extends Controller
{
    public function __construct(private readonly OurValueRepositoryInterface $repo)
    {
        // $this->authorizeResource(OurValue::class, 'our_value');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', Statistics::class);

        $result =  AutoFilterAndSortService::dynamicSearchFromRequest(
            model: OurValue::class,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations', 'image']);
            },
        );

        $result['data'] =  collect($result['data'])->map(function ($item) {
            return resolve(OurValueResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    public function store(Store $request): JsonResponse
    {
        $ourValue = $this->repo->store($request->validated());
        return successResponse(
            message: translate('cms.messages.added_successfully'),
            data: resolve(OurValueResource::class, ['resource' => $this->repo->show($ourValue)])->withFields(request()->get('fields')),
        );
    }

    public function show(OurValue $ourValue): JsonResponse
    {

        return  successResponse(
            data: resolve(OurValueResource::class, ['resource' => $ourValue])->withFields(request()->get('fields')),
        );
    }

    public function update(Update $request, OurValue $ourValue): JsonResponse
    {
        $updatedOurValue = $this->repo->update($ourValue, $request->validated());
        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(OurValueResource::class, ['resource' => $updatedOurValue])->withFields(request()->get('fields')),
        );
    }

    public function updateAll(UpdateAll $request)
    {
        // $this->authorize('updateAny', OurValue::class);

        $ids = [];
        $updatedOurValue = [];
        foreach ($request->all() as $featureData) {
            if (isset($featureData['id'])) {
                $ids[] = $featureData['id'];
                $ourValue = OurValue::findOrFail($featureData['id']);
                // $this->authorize('update', $ourValue);
                $updatedOurValue[] = $this->repo->update($ourValue, $featureData);
            }
        }

        $updatedOurValue = OurValue::whereIn('id', $ids)->with(['translations', 'image'])->get();

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: collect($updatedOurValue)->map(function ($item) {
                return resolve(OurValueResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    public function updateImage(Request $request, OurValue $ourValue): JsonResponse
    {
        // $this->authorize('manageImages', $ourValue);

        $validated = $request->validate([
            'image' => ['required'],
        ]);

        $updatedOurValue = $this->repo->update($ourValue, $validated);
        $updatedOurValue->load(['image']);

        return successResponse(
            message: translate('cms.messages.image_updated_successfully'),
            data: resolve(OurValueResource::class, ['resource' => $updatedOurValue])->withFields(request()->get('fields')),
        );
    }

    public function destroy(OurValue $ourValue): JsonResponse
    {
        $this->repo->delete($ourValue);
        return successResponse(message: translate('cms.messages.deleted_successfully'));
    }
}
