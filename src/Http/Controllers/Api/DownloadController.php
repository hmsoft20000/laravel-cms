<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Downloads\{StoreDownloadRequest, UpdateAllDownloadRequest, UpdateDownloadRequest};
use HMsoft\Cms\Http\Resources\Api\DownloadResource;
use HMsoft\Cms\Models\Shared\Download;
use HMsoft\Cms\Repositories\Contracts\DownloadRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: new Download(),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($owner) {
                $query->where('owner_type', $owner->getMorphClass());
                $query->where('owner_id', $owner->id);
                $query->with([
                    'translations',
                ]);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return new DownloadResource($item);
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
            message: translate('cms::messages.added_successfully'),
            data: new DownloadResource($download),
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
        return successResponse(data: new DownloadResource($this->repository->show($download)));
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
            message: translate('cms::messages.updated_successfully'),
            data: new DownloadResource($updatedDownload)
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
        return successResponse(message: translate('cms::messages.deleted_successfully'));
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
            message: translate('cms::messages.updated_successfully'),
            data: DownloadResource::collection($updatedDownloads)
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
            message: translate('cms::messages.file_updated_successfully'),
            data: new DownloadResource($updatedDownload)
        );
    }
}
