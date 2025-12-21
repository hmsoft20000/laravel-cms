<?php

namespace HMsoft\Cms\Http\Controllers\Api\Shop;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Shop\{StoreItemRequest, UpdateItemRequest, UpdateAllItemRequest, AttachDownloadsRequest}; // We will create these
use HMsoft\Cms\Http\Resources\Api\Shop\ItemResource; // We will create this
use HMsoft\Cms\Models\Shop\Item;
use HMsoft\Cms\Repositories\Contracts\ItemRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct(
        private readonly ItemRepositoryInterface $repo
    ) {}

    /**
     * Display a listing of the items.
     */
    public function index(): JsonResponse
    {
        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: resolve(Item::class),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) {
                // Add any extra query logic, e.g., checking permissions
                // $query->where('t_main.is_active', true);

                $query->with([
                    'translations',
                    'media',
                    'categories.translations',
                    'attributeValues.attribute.translations',
                    'attributeValues.attribute.options.translations',
                    'attributeValues.selectedOptions.option.translations',
                    // Add other relations as needed for the list view
                    'variations.attributeOptions.translations',
                    'relationships.relatedItem.translations',
                ]);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return resolve(ItemResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(StoreItemRequest $request): JsonResponse
    {
        $item = $this->repo->store($request->validated());
        return successResponse(
            message: translate('cms.messages.added_successfully'), // Use your translation helper
            data: resolve(ItemResource::class, ['resource' => $this->repo->show($item)])->withFields(request()->get('fields')),
        );
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item): JsonResponse
    {
        $item = $this->repo->show($item);
        return successResponse(data: resolve(ItemResource::class, ['resource' => $item])->withFields(request()->get('fields')));
    }

    /**
     * Update the specified item in storage.
     */
    public function update(UpdateItemRequest $request, Item $item): JsonResponse
    {
        $updatedItem = $this->repo->update($item, $request->validated());

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(ItemResource::class, ['resource' => $updatedItem])->withFields(request()->get('fields'))
        );
    }

    /**
     * Update multiple items.
     */
    public function updateAll(UpdateAllItemRequest $request): JsonResponse
    {
        $updatedItems = [];
        foreach ($request->all() as $itemData) {
            if (isset($itemData['id'])) {
                $item = Item::findOrFail($itemData['id']);
                $updatedItems[] = $this->repo->update($item, $itemData);
            }
        }

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: collect($updatedItems)->map(function ($item) {
                return resolve(ItemResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item): JsonResponse
    {
        $this->repo->delete($item);
        return successResponse(
            message: translate('cms.messages.deleted_successfully'),
        );
    }

    /**
     * قم بربط ملفات التحميل بمنتج معين
     *
     * @param Request $request
     * @param Item $item
     * @return JsonResponse
     */
    public function attachDownloads(AttachDownloadsRequest $request, Item $item): JsonResponse
    {
        $this->repo->attachDownloads($item, $request->input('download_item_ids', []));

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
        );
    }
}
