<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Team\{Store, Update, Delete, UpdateAll};
use HMsoft\Cms\Http\Resources\Api\TeamResource;
use HMsoft\Cms\Models\Team\Team;
use HMsoft\Cms\Repositories\Contracts\TeamRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{

    public function __construct(private readonly TeamRepositoryInterface $repo) {}

    public function index(Request $request)
    {
        // $this->authorize('viewAny', Team::class);

        $result =  AutoFilterAndSortService::dynamicSearchFromRequest(
            model: Team::class,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        );

        $result['data'] =  collect($result['data'])->map(function ($item) {
            return (new TeamResource($item))->withFields(request()->get('fields'));
        })->all();

        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    public function show(Team $team)
    {
        // $this->authorize('view', $team);

        $team->load(['translations']);
        return  successResponse(
            data: (new TeamResource($team))->withFields(request()->get('fields'))
        );
    }

    public function store(Store $request)
    {
        // $this->authorize('create', Team::class);

        $validated = $request->validated();
        $team = $this->repo->store($validated);
        $team->load(['translations']);
        return  successResponse(
            message: translate('cms::messages.added_successfully'),
            data: (new TeamResource($team))->withFields(request()->get('fields'))
        );
    }

    public function update(Update $request, Team $team)
    {
        // $this->authorize('update', $team);

        $validated = $request->validated();
        $team = $this->repo->update($team, $validated);
        $team->load(['translations']);
        return  successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: (new TeamResource($team))->withFields(request()->get('fields'))
        );
    }

    public function updateAll(UpdateAll $request)
    {
        // $this->authorize('updateAny', Team::class);

        $updatedStatistics = [];
        foreach ($request->all() as $featureData) {
            if (isset($featureData['id'])) {
                $team = Team::findOrFail($featureData['id']);
                // $this->authorize('update', $team);
                $updatedStatistics[] = $this->repo->update($team, $featureData);
            }
        }

        // Load translations for all updated statistics
        $updatedStatistics = collect($updatedStatistics)->map(function ($team) {
            $team->load(['translations']);
            return $team;
        });

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: TeamResource::collection($updatedStatistics)
        );
    }


    public function updateImage(Request $request, Team $team): JsonResponse
    {
        // $this->authorize('manageImages', $team);

        $validated = $request->validate([
            'image' => ['required', 'image', 'max:2048'],
        ]);

        $updatedTeam = $this->repo->update($team, $validated);

        return successResponse(
            message: translate('cms::messages.image_updated_successfully'),
            data: new TeamResource($updatedTeam)
        );
    }

    public function destroy(Delete $request, Team $team)
    {
        // $this->authorize('delete', $team);

        $this->repo->destroy($team);
        return  successResponse(
            message: translate('cms::messages.deleted_successfully'),
        );
    }
}
