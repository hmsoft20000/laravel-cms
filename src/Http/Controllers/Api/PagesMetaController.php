<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\PageMeta\{Store, Update, Delete, UpdateAll};
use HMsoft\Cms\Http\Resources\Api\PageMetaResource;
use HMsoft\Cms\Models\PageMeta\PageMeta;
use HMsoft\Cms\Repositories\Contracts\PagesMetaRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\Request;

class PagesMetaController extends Controller
{
    public function __construct(
        private readonly PagesMetaRepositoryInterface  $repo,
    ) {}

    public function index(Request $request)
    {

        $result =  AutoFilterAndSortService::dynamicSearchFromRequest(
            model: PageMeta::class,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        );
        $result['data'] =  collect($result['data'])->map(function ($item) {
            return resolve(PageMetaResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    public function show(PageMeta $pageMeta)
    {
        $pageMeta->load(['translations']);
        return  successResponse(
            data: resolve(PageMetaResource::class, ['resource' => $pageMeta])->withFields(request()->get('fields'))
        );
    }

    public function store(Store $request)
    {
        $validated = $request->validated();
        $pageMeta = $this->repo->store($validated);
        $pageMeta->load(['translations']);
        return  successResponse(
            message: translate('cms::messages.added_successfully'),
            data: resolve(PageMetaResource::class, ['resource' => $pageMeta])->withFields(request()->get('fields'))
        );
    }

    public function update(Update $request, PageMeta $pageMeta)
    {
        $validated = $request->validated();
        $pageMeta = $this->repo->update($pageMeta, $validated);
        return  successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: resolve(PageMetaResource::class, ['resource' => $pageMeta])->withFields(request()->get('fields'))
        );
    }

    public function updateAll(UpdateAll $request, PageMeta $pageMeta)
    {
        $validated = $request->validated();
        $this->repo->updateMultiple($validated);

        $result =  AutoFilterAndSortService::dynamicSearchFromRequest(
            model: PageMeta::class,
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with(['translations']);
            },
        );
        $result['data'] =  collect($result['data'])->map(function ($item) {
            return resolve(PageMetaResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    public function destroy(Delete $request, PageMeta $pageMeta)
    {
        $this->repo->destroy($pageMeta);
        return  successResponse(
            message: translate('cms::messages.deleted_successfully'),
        );
    }
}
