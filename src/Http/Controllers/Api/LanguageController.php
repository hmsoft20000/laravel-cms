<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use Illuminate\Http\Request;
use HMsoft\Cms\Http\Requests\Lang\{Store, Update, UpdateAll, Delete};
use HMsoft\Cms\Http\Resources\Api\LangResource;
use HMsoft\Cms\Models\Lang;
use HMsoft\Cms\Repositories\Contracts\LangRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{

    public function __construct(
        private readonly LangRepositoryInterface $repo,
    ) {}


    public function index(Request $request)
    {
        // $this->authorize('viewAny', Lang::class);

        $result =  AutoFilterAndSortService::dynamicSearchFromRequest(
            model: Lang::class,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
            },
        );

        $result['data'] =  collect($result['data'])->map(function ($item) {
            return (new LangResource($item))->withFields(request()->get('fields'));
        })->all();

        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    public function show(Lang $lang)
    {
        // $this->authorize('view', $lang);

        $lang->load(['translations']);
        return  successResponse(
            data: (new LangResource($lang))->withFields(request()->get('fields'))
        );
    }

    public function store(Store $request)
    {
        // $this->authorize('create', Lang::class);

        $validated = $request->validated();
        $lang = $this->repo->store($validated);
        $lang->load(['translations']);
        return  successResponse(
            message: translate('cms::messages.added_successfully'),
            data: (new LangResource($lang))->withFields(request()->get('fields'))
        );
    }

    public function update(Update $request, Lang $lang)
    {
        // $this->authorize('update', $lang);

        $validated = $request->validated();
        $lang = $this->repo->update($lang, $validated);
        $lang->load(['translations']);
        return  successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: (new LangResource($lang))->withFields(request()->get('fields'))
        );
    }

    public function updateAll(UpdateAll $request)
    {
        // $this->authorize('updateAny', Lang::class);

        $updatedLangs = [];
        foreach ($request->all() as $langData) {
            if (isset($langData['id'])) {
                $lang = Lang::findOrFail($langData['id']);
                // $this->authorize('update', $lang);
                $updatedLangs[] = $this->repo->update($lang, $langData);
            }
        }

        // Load translations for all updated langs
        $updatedLangs = collect($updatedLangs)->map(function ($lang) {
            $lang->load(['translations']);
            return $lang;
        });

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: LangResource::collection($updatedLangs)
        );
    }

    public function updateImage(Request $request, Lang $lang): JsonResponse
    {
        // $this->authorize('manageImages', $lang);

        $validated = $request->validate([
            'image' => ['required', 'image', 'max:2048'],
        ]);

        $updatedLang = $this->repo->update($lang, $validated);

        return successResponse(
            message: translate('cms::messages.image_updated_successfully'),
            data: new LangResource($updatedLang)
        );
    }

    public function destroy(Delete $request, Lang $lang)
    {
        // $this->authorize('delete', $lang);

        $this->repo->destroy($lang);
        return  successResponse(
            message: translate('cms::messages.deleted_successfully'),
        );
    }
}
