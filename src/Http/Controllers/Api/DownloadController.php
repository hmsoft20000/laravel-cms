<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Downloads\{StoreDownloadRequest, UpdateAllDownloadRequest, UpdateDownloadRequest};
use HMsoft\Cms\Http\Resources\Api\DownloadItemResource;
use HMsoft\Cms\Http\Resources\Api\DownloadResource;
use HMsoft\Cms\Models\Shared\Download;
use HMsoft\Cms\Models\Shared\DownloadItem;
use HMsoft\Cms\Repositories\Contracts\DownloadRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DownloadController extends Controller
{


    public function __construct(
        private readonly DownloadRepositoryInterface $repository
    ) {}

    /**
     * Display a listing of the resource, scoped by the owner type from the route.
     * @param Request $request
     * @param Model $owner The magic happens here. This will be an instance of Post OR Product.
     * @return JsonResponse
     */
    public function index(Request $request, Model $owner): JsonResponse
    {
        // $this->authorize('viewAny', [Feature::class, $owner]);

        // $result = AutoFilterAndSortService::dynamicSearchFromRequest(
        //     model: resolve(Download::class),
        //     extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($owner) {
        //         $query->where('owner_type', $owner->getMorphClass());
        //         $query->where('owner_id', $owner->id);
        //         $query->with([
        //             'downloadItem.links',
        //             'downloadItem.translations',
        //         ]);
        //     },
        // );

        DB::listen(function ($query) {
            Log::info('SQL:', [
                'query'    => $query->sql,
                'bindings' => $query->bindings,
                'time_ms'  => $query->time,
            ]);
        });
        
        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: resolve(DownloadItem::class),
            extraOperation: function ($query) use ($owner) {
                // [FIX]: نقوم بعمل Join يدوي مع الجدول الوسيط لكي تعمل الفلترة
                // $query->join('downloads', 'download_items.id', '=', 'downloads.download_item_id')
                //     ->where('downloads.owner_id', $owner->id)
                //     ->where('downloads.owner_type', $owner->getMorphClass())
                //     ->select('download_items.*'); // نحدد أعمدة المدونات فقط لتجنب تضارب الـ ID

                $query->whereHas('downloads', function ($q) use ($owner) {
                    $q->where('owner_id', $owner->id)
                        ->where('owner_type', $owner->getMorphClass());
                });
                // إضافة العلاقات المطلوبة
                $query->withAggregate([
                    'downloads as download_id' => function ($q) use ($owner) {
                        $q->where('owner_id', $owner->id)
                            ->where('owner_type', $owner->getMorphClass());
                    }
                ], 'id');

                $query->with(['links', 'translations']);
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
     * @param StoreDownloadRequest $request
     * @param Model $owner
     * @return JsonResponse
     */
    public function store(StoreDownloadRequest $request, Model $owner): JsonResponse
    {
        // $this->authorize('create', Download::class);
        $validated = $request->validated();
        $ownerData = [
            'owner_id' => $owner->id,
            'owner_type' => $owner->getMorphClass(),
        ];
        $validated = array_merge($validated, $ownerData);
        $download = $this->repository->store($validated);
        return successResponse(
            message: translate('cms.messages.added_successfully'),
            data: resolve(DownloadResource::class, ['resource' => $download])->withFields(request()->get('fields')),
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource and check if it belongs to the owner model.
     * @param Model $owner
     * @param Download $download
     * @return JsonResponse
     */
    public function show(Model $owner, Download $download): JsonResponse
    {
        // $this->authorize('view', $feature);

        // Optional: Add a check to ensure the plan belongs to the correct type
        if ($download->owner_type != $owner->getMorphClass() || $download->owner_id != $owner->id) {
            abort(404);
        }
        return successResponse(data: resolve(DownloadResource::class, ['resource' => $this->repository->show($download)])->withFields(request()->get('fields')));
    }

    /**
     * Update the specified resource in storage and check if it belongs to the owner model.
     *
     * @param UpdateDownloadRequest $request
     * @param Model $owner
     * @param Download $download
     * @return JsonResponse
     */
    public function update(UpdateDownloadRequest $request, Model $owner, Download $download): JsonResponse
    {
        // $this->authorize('update', $download);

        // Optional: Add a check to ensure the download belongs to the correct type
        if ($download->owner_type != $owner->getMorphClass() || $download->owner_id != $owner->id) {
            abort(404);
        }
        $updatedDownload = $this->repository->update($download, $request->validated());
        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(DownloadResource::class, ['resource' => $updatedDownload])->withFields(request()->get('fields'))
        );
    }

    /**
     * Remove the specified resource from storage and check if it belongs to the owner model.
     *
     * @param Model $owner
     * @param Download $download
     * @return JsonResponse
     */
    public function destroy(Model $owner, Download $download): JsonResponse
    {
        // $this->authorize('delete', $download);

        // Optional: Add a check to ensure the download belongs to the correct type
        if ($download->owner_type != $owner->getMorphClass() || $download->owner_id != $owner->id) {
            abort(404);
        }
        $this->repository->delete($download);
        return successResponse(message: translate('cms.messages.deleted_successfully'));
    }

    /**
     * Update all the features for the owner model.
     *
     * @param Request $request
     * @param Model $owner
     * @return JsonResponse
     */
    public function updateAll(UpdateAllDownloadRequest $request, Model $owner): JsonResponse
    {
        // $this->authorize('bulkUpdate', Feature::class);

        $updatedDownloads = [];
        foreach ($request->all() as $downloadData) {
            if (isset($downloadData['id'])) {
                $download = Download::findOrFail($downloadData['id']);
                if ($download->owner_type != $owner->getMorphClass() || $download->owner_id != $owner->id) {
                    abort(404);
                }
                $updatedDownloads[] = $this->repository->update($download, $downloadData);
            }
        }

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: collect($updatedDownloads)->map(function ($item) {
                return resolve(DownloadResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    /**
     * Update the file of the specified resource and check if it belongs to the owner model.
     *
     * @param Request $request
     * @param Model $owner
     * @param Download $download
     * @return JsonResponse
     */
    public function updateFile(Request $request, Model $owner, Download $download): JsonResponse
    {
        // $this->authorize('manageMedia', $download);

        if ($download->owner_type != $owner->getMorphClass() || $download->owner_id != $owner->id) {
            abort(404);
        }

        $validated = $request->validate([
            'file' => ['required'],
        ]);

        $updatedDownload = $this->repository->update($download, $validated);

        return successResponse(
            message: translate('cms.messages.file_updated_successfully'),
            data: resolve(DownloadResource::class, ['resource' => $updatedDownload])->withFields(request()->get('fields')),
        );
    }
}
