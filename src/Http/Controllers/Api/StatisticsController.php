<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Statistics\{Store, Update, Delete, UpdateAll};
use HMsoft\Cms\Http\Resources\Api\StatisticsResource;
use HMsoft\Cms\Models\Statistics\Statistics;
use HMsoft\Cms\Repositories\Contracts\StatisticsRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{

    public function __construct(private readonly StatisticsRepositoryInterface $repo) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', Statistics::class);

        $result =  AutoFilterAndSortService::dynamicSearchFromRequest(
            model: Statistics::class,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations', 'image']);
            },
        );

        $result['data'] =  collect($result['data'])->map(function ($item) {
            return resolve(StatisticsResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    public function show(Statistics $statistics)
    {
        // $this->authorize('view', $statistics);

        $statistics = $this->repo->show($statistics);

        return  successResponse(
            data: resolve(StatisticsResource::class, ['resource' => $statistics])->withFields(request()->get('fields'))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request)
    {
        // $this->authorize('create', Statistics::class);

        $validated = $request->validated();
        $statistics = $this->repo->store($validated);
        $statistics->load(['translations', 'image']);
        return  successResponse(
            message: translate('cms::messages.added_successfully'),
            data: resolve(StatisticsResource::class, ['resource' => $statistics])->withFields(request()->get('fields'))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, Statistics $statistics)
    {
        // $this->authorize('update', $statistics);

        $validated = $request->validated();
        $statistics = $this->repo->update($statistics, $validated);
        $statistics->load(['translations', 'image']);
        return  successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: resolve(StatisticsResource::class, ['resource' => $statistics])->withFields(request()->get('fields'))
        );
    }

    public function updateAll(UpdateAll $request)
    {
        // $this->authorize('updateAny', Statistics::class);

        $updatedStatistics = [];

        foreach ($request->all() as $featureData) {
            if (isset($featureData['id'])) {
                $statistics = Statistics::findOrFail($featureData['id']);
                // $this->authorize('update', $statistics);
                $updatedStatistics[] = $this->repo->update($statistics, $featureData);
            }
        }

        // Load translations and image for all updated statistics
        $updatedStatistics = collect($updatedStatistics)->map(function ($statistics) {
            $statistics->load(['translations', 'image']);
            return $statistics;
        });

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: collect($updatedStatistics)->map(function ($item) {
                return resolve(StatisticsResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    public function updateImage(Request $request, Statistics $statistics): JsonResponse
    {
        // $this->authorize('manageImages', $statistics);

        $validated = $request->validate([
            'image' => ['required', 'image', 'max:2048'],
        ]);

        $updatedStatistics = $this->repo->update($statistics, $validated);
        $updatedStatistics->load(['image']);

        return successResponse(
            message: translate('cms::messages.image_updated_successfully'),
            data: resolve(StatisticsResource::class, ['resource' => $updatedStatistics])->withFields(request()->get('fields')),
        );
    }

    public function destroy(Delete $request, Statistics $statistics)
    {
        // $this->authorize('delete', $statistics);

        $this->repo->destroy($statistics);
        return  successResponse(
            message: translate('cms::messages.deleted_successfully'),
        );
    }
}
