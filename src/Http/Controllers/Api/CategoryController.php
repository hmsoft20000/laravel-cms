<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Categories\{StoreCategoryRequest, UpdateCategoryRequest, UpdateAllCategoryRequest};
use HMsoft\Cms\Http\Resources\Api\CategoryResource;
use HMsoft\Cms\Models\Shared\Category;
use HMsoft\Cms\Repositories\Contracts\CategoryRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{


    public function __construct(
        private readonly CategoryRepositoryInterface $repository
    ) {}

    public function index(Request $request): JsonResponse
    {
        // $this->authorize('viewAny', Category::class);
        $type = $request->route('type');

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: new Category(),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($type) {
                $query->ofType($type);
                $query->with(['translations', 'sector']);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return (new CategoryResource($item))->withFields(request()->get('fields'));
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        // $this->authorize('create', Category::class);

        $category = $this->repository->store($request->validated());
        return successResponse(
            message: translate('cms::messages.added_successfully'),
            data: new CategoryResource($this->repository->show($category)),
            code: Response::HTTP_CREATED
        );
    }

    public function show(Category $category): JsonResponse
    {
        // $this->authorize('view', $category);

        return successResponse(
            data: new CategoryResource($this->repository->show($category))
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        // $this->authorize('update', $category);

        $updatedCategory = $this->repository->update($category, $request->validated());
        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: new CategoryResource($updatedCategory)
        );
    }

    public function updateAll(UpdateAllCategoryRequest $request): JsonResponse
    {
        // $this->authorize('updateAny', Category::class);

        $updatedCategories = [];
        foreach ($request->all() as $categoryData) {
            if (isset($categoryData['id'])) {
                $category = Category::findOrFail($categoryData['id']);
                // $this->authorize('update', $category);
                $updatedCategories[] = $this->repository->update($category, $categoryData);
            }
        }

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: CategoryResource::collection($updatedCategories)
        );
    }

    public function destroy(Category $category): JsonResponse
    {
        // $this->authorize('delete', $category);

        $this->repository->delete($category);
        return successResponse(message: translate('cms::messages.deleted_successfully'));
    }
}
