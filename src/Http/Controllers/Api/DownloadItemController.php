<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\DownloadItem\{Store, Update, Delete, UpdateAll};
use HMsoft\Cms\Http\Resources\Api\DownloadItemResource;
use HMsoft\Cms\Models\Shared\DownloadItem;
use HMsoft\Cms\Repositories\Contracts\DownloadItemRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DownloadItemController extends Controller
{

    public function __construct(
        private DownloadItem $downloadItem,
        private DownloadItemRepositoryInterface $repo,
    ) {}


    public function index(Request $request): JsonResponse
    {
        // $this->authorize('viewAny', [Feature::class, $owner]);

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: resolve(DownloadItem::class),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) {
                $query->with([
                    'translations',
                    'categories',
                    'media',
                ]);
                $query->withCount('links');
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return resolve(DownloadItemResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $downloadItem = $this->repo->store($request->validated());

        return successResponse(
            message: __('cms.download_items.download_item_created'),
            data: resolve(DownloadItemResource::class, ['resource' => $downloadItem])->withFields(request()->get('fields')),
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $downloadItem
     * @return \Illuminate\Http\Response
     */
    public function show($downloadItem)
    {
        $downloadItem = $this->downloadItem->findOrFail($downloadItem);
        $downloadItem = $this->repo->show($downloadItem);
        // info($downloadItem);
        return successResponse(
            data: resolve(DownloadItemResource::class, ['resource' => $downloadItem])->withFields(request()->get('fields')),
        );
    }
    public function update(Update $request, $downloadItem)
    {
        $downloadItem = $this->downloadItem->findOrFail($downloadItem);
        $downloadItem = $this->repo->update($downloadItem, $request->validated());
        return successResponse(
            message: __('cms.download_items.download_item_updated'),
            data: resolve(DownloadItemResource::class, ['resource' => $downloadItem])->withFields(request()->get('fields')),
        );
    }

    public function updateImage(Request $request, DownloadItem $downloadItem): JsonResponse
    {

        $rules = [
            'delete_image' => ['sometimes'],
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = ['required'];
        } elseif ($request->filled('image')) {
            $rules['image'] = ['required'];
        }

        $validated = $request->validate($rules);

        $updatedDownloadItem = $this->repo->update($downloadItem, $validated);

        return successResponse(
            message: translate('cms.messages.image_updated_successfully'),
            data: resolve(DownloadItemResource::class, ['resource' => $updatedDownloadItem])->withFields(request()->get('fields')),

        );
    }

    public function destroy(Delete $request, $id)
    {
        $this->repo->delete($this->downloadItem->findOrFail($id));
        return successResponse(
            message: __('cms.download_items.download_item_deleted'),
        );
    }
}
