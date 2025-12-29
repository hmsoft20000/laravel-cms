<?php

namespace HMsoft\Cms\Http\Controllers\Api\Shop;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Shop\Addons\StoreItemAddonRequest;
use HMsoft\Cms\Http\Requests\Shop\Addons\UpdateItemAddonRequest;
use HMsoft\Cms\Http\Resources\Api\Shop\ItemAddonResource;
use HMsoft\Cms\Models\Shop\Item;
use HMsoft\Cms\Models\Shop\ItemAddon;
use HMsoft\Cms\Repositories\Contracts\ItemAddonRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemAddonController extends Controller
{
    public function __construct(
        private readonly ItemAddonRepositoryInterface $repository
    ) {}

    /**
     * جلب كافة الإضافات التابعة لمنتج معين
     */
    public function index(Request $request, Item $item): JsonResponse
    {
        // // يمكنك إضافة صلاحيات هنا
        // // $this->authorize('viewAny', [ItemAddon::class, $item]);

        // $addons = $item->addons()
        //     ->with(['translations', 'options.translations'])
        //     ->orderBy('sort_number')
        //     ->get();

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: ItemAddon::class,
            filters: [],
            sorting: [],
            globalFilter: "",
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) use ($item) {
                $query->where('item_id', $item->id);
                $query->with(['translations', 'options.translations']);
            },
        );
        $result['data'] = collect($result['data'])->map(function ($item) use ($request) {
            return resolve(ItemAddonResource::class, ['resource' => $item])->toArray($request);
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );


        return successResponse(
            data: ItemAddonResource::collection($addons)
        );
    }

    /**
     * إضافة جديد للمنتج
     */
    public function store(StoreItemAddonRequest $request, Item $item): JsonResponse
    {
        // $this->authorize('create', ItemAddon::class);

        $addon = $this->repository->store($item, $request->validated());

        return successResponse(
            message: translate('cms.messages.added_successfully'), // تأكد من وجود مفتاح الترجمة هذا أو استخدم 'Saved successfully'
            data: resolve(ItemAddonResource::class, ['resource' => $addon])
        );
    }

    /**
     * عرض تفاصيل إضافة واحدة
     */
    public function show(Item $item, ItemAddon $addon): JsonResponse
    {
        $this->ensureOwnership($item, $addon);

        $addon->load(['translations', 'options.translations']);

        return successResponse(
            data: resolve(ItemAddonResource::class, ['resource' => $addon])
        );
    }

    /**
     * تعديل إضافة موجودة
     */
    public function update(UpdateItemAddonRequest $request, Item $item, ItemAddon $addon): JsonResponse
    {
        $this->ensureOwnership($item, $addon);

        // $this->authorize('update', $addon);

        $updatedAddon = $this->repository->update($addon, $request->validated());

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(ItemAddonResource::class, ['resource' => $updatedAddon])
        );
    }

    /**
     * حذف إضافة
     */
    public function destroy(Item $item, ItemAddon $addon): JsonResponse
    {
        $this->ensureOwnership($item, $addon);

        // $this->authorize('delete', $addon);

        $this->repository->delete($addon);

        return successResponse(
            message: translate('cms.messages.deleted_successfully')
        );
    }

    /**
     * التأكد من أن الإضافة تابعة للمنتج الممرر في الرابط
     * (Nested Resource Security)
     */
    private function ensureOwnership(Item $item, ItemAddon $addon): void
    {
        if ($addon->item_id !== $item->id) {
            abort(404);
        }
    }
}
