<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Legal\{Update};
use HMsoft\Cms\Http\Resources\Api\LegalResource;
use HMsoft\Cms\Models\Legal\Legal;
use HMsoft\Cms\Repositories\Contracts\LegalRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\Request;

class LegalsController extends Controller
{

    protected string|null $type;
    public function __construct(
        private readonly LegalRepositoryInterface  $repo,
    ) {
        $this->type = request()->route('type');
    }

    public function index(Request $request)
    {
        // $this->authorize('viewAny', Legal::class);

        $result =  AutoFilterAndSortService::dynamicSearchFromRequest(
            model: Legal::class,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with([
                    'translations',
                    'media',
                ]);
                if ($this->type != 'legal') {
                    $query->where('type', $this->type);
                }
            },
        );

        $result['data'] =  collect($result['data'])->map(function ($item) {
            return (new LegalResource($item))->withFields(request()->get('fields'));
        })->all();

        if ($this->type != 'legal' && count($result['data']) > 0) {
            $result['data'] = $result['data'][0];
        }

        return  successResponse(
            data: $result['data'] ?? [],
            pagination: $result['pagination'],
        );
    }

    public function update(Update $request)
    {
        $legal = Legal::where('type', $this->type)->first();
        // $this->authorize('update', $legal);
        $validated = $request->validated();
        $legal = $this->repo->update($legal, $validated);

        return  successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: (new LegalResource($legal))->withFields(request()->get('fields'))
        );
    }
}
