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
            model: resolve(Category::class),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($type) {
                $query->ofType($type);
                $query->with(['translations', 'sector']);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return resolve(CategoryResource::class, ['resource' => $item])->withFields(request()->get('fields'));
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
            data: resolve(CategoryResource::class, ['resource' => $this->repository->show($category)])->withFields(request()->get('fields')),
            code: Response::HTTP_CREATED
        );
    }

    public function show(Category $category): JsonResponse
    {
        // $this->authorize('view', $category);

        return successResponse(
            data: resolve(CategoryResource::class, ['resource' => $this->repository->show($category)])->withFields(request()->get('fields'))
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        // $this->authorize('update', $category);

        $updatedCategory = $this->repository->update($category, $request->validated());
        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: resolve(CategoryResource::class, ['resource' => $updatedCategory])->withFields(request()->get('fields'))
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
            data: collect($updatedCategories)->map(function ($item) {
                return resolve(CategoryResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    public function updateImage(Request $request, Category $category): JsonResponse
    {

        $rules = [
            'delete_image' => ['sometimes', 'boolean'],
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = ['required', 'image', 'max:2048'];
        } elseif ($request->filled('image')) {
            $rules['image'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $updatedCategory = $this->repository->update($category, $validated);

        return successResponse(
            message: translate('cms::messages.image_updated_successfully'),
            data: resolve(CategoryResource::class, ['resource' => $updatedCategory])->withFields(request()->get('fields')),

        );
    }

    public function destroy(Category $category): JsonResponse
    {
        // $this->authorize('delete', $category);

        $this->repository->delete($category);
        return successResponse(message: translate('cms::messages.deleted_successfully'));
    }
}
