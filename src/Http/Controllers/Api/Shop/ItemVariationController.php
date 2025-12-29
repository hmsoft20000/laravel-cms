<?php

namespace HMsoft\Cms\Http\Controllers\Api\Shop;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Shop\Variations\StoreItemVariationRequest;
use HMsoft\Cms\Http\Requests\Shop\Variations\UpdateItemVariationRequest;
use HMsoft\Cms\Http\Resources\Api\Shop\ItemVariationResource;
use HMsoft\Cms\Models\Shop\Item;
use HMsoft\Cms\Models\Shop\ItemVariation;
use HMsoft\Cms\Repositories\Contracts\ItemVariationRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemVariationController extends Controller
{
    public function __construct(
        private readonly ItemVariationRepositoryInterface $repository
    ) {}

    /**
     * عرض كافة التنويعات لمنتج معين
     */
    public function index(Request $request, Item $item): JsonResponse
    {
        // $this->authorize('viewAny', [ItemVariation::class, $item]);


        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: ItemVariation::class,
            filters: [],
            sorting: [],
            globalFilter: "",
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) use ($item) {
                $query->where('item_id', $item->id);
                $query->with(['attributeOptions.translations', 'attributeOptions.attribute.translations', 'media']);
            },
        );
        // $variations = $item->variations()
        //     ->with(['attributeOptions.translations', 'attributeOptions.attribute.translations', 'media'])
        //     ->get();

        $result['data'] = collect($result['data'])->map(function ($item) use ($request) {
            return resolve(ItemVariationResource::class, ['resource' => $item])->toArray($request);
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * إضافة تنويعة جديدة
     */
    public function store(StoreItemVariationRequest $request, Item $item): JsonResponse
    {
        // $this->authorize('create', ItemVariation::class);

        $variation = $this->repository->store($item, $request->validated());

        return successResponse(
            message: translate('cms.messages.added_successfully'),
            data: resolve(ItemVariationResource::class, ['resource' => $variation])
        );
    }

    /**
     * عرض تفاصيل تنويعة واحدة
     */
    public function show(Item $item, ItemVariation $variation): JsonResponse
    {
        $this->ensureOwnership($item, $variation);

        $variation->load(['attributeOptions.translations', 'attributeOptions.attribute.translations', 'media']);

        return successResponse(
            data: resolve(ItemVariationResource::class, ['resource' => $variation])
        );
    }

    /**
     * تحديث تنويعة
     */
    public function update(UpdateItemVariationRequest $request, Item $item, ItemVariation $variation): JsonResponse
    {
        $this->ensureOwnership($item, $variation);

        // $this->authorize('update', $variation);

        $updatedVariation = $this->repository->update($variation, $request->validated());

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(ItemVariationResource::class, ['resource' => $updatedVariation])
        );
    }

    /**
     * حذف تنويعة
     */
    public function destroy(Item $item, ItemVariation $variation): JsonResponse
    {
        $this->ensureOwnership($item, $variation);

        // $this->authorize('delete', $variation);

        $this->repository->delete($variation);

        return successResponse(
            message: translate('cms.messages.deleted_successfully')
        );
    }

    /**
     * التحقق من تبعية الـ Variation للـ Item
     */
    private function ensureOwnership(Item $item, ItemVariation $variation): void
    {
        if ($variation->item_id !== $item->id) {
            abort(404);
        }
    }
}
