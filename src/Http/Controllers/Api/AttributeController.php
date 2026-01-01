<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Attributes\{StoreAttributeRequest, UpdateAttributeRequest, UpdateAllAttributeRequest};
use HMsoft\Cms\Http\Resources\Api\AttributeResource;
use HMsoft\Cms\Models\Shared\Attribute;
use HMsoft\Cms\Repositories\Contracts\AttributeRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AttributeController extends Controller
{


    public function __construct(
        private readonly AttributeRepositoryInterface $repository
    ) {}

    /**
     * Display a listing of the resource.
     * Can be filtered by `scope` (e.g., ?filter[scope]=portfolio).
     */
    public function index(Request $request): JsonResponse
    {

        $scope = request()->route('type');


        // This logic is preserved from your preferred pattern
        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: resolve(Attribute::class),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($scope) {
                $query->ofScope($scope);
                $query->with(['translations', 'options.translations', 'categories.translations']);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return resolve(AttributeResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttributeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $attribute = $this->repository->store($validated);
        return successResponse(
            message: translate('cms.messages.added_successfully'),
            data: resolve(AttributeResource::class, ['resource' => $this->repository->show($attribute)])->withFields(request()->get('fields')),
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute): JsonResponse
    {
        return successResponse(
            data: resolve(AttributeResource::class, ['resource' => $this->repository->show($attribute)])->withFields(request()->get('fields')),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute): JsonResponse
    {
        $updatedAttribute = $this->repository->update($attribute, $request->validated());
        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(AttributeResource::class, ['resource' => $updatedAttribute])->withFields(request()->get('fields')),
        );
    }

    public function updateAll(UpdateAllAttributeRequest $request): JsonResponse
    {
        // $this->authorize('updateAny', Attribute::class);

        $updatedAttributes = [];
        foreach ($request->all() as $attributeData) {
            if (isset($attributeData['id'])) {
                $attribute = Attribute::findOrFail($attributeData['id']);
                // $this->authorize('update', $attribute);
                $updatedAttributes[] = $this->repository->update($attribute, $attributeData);
            }
        }

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: collect($updatedAttributes)->map(function ($item) {
                return resolve(AttributeResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    public function updateImage(Request $request, Attribute $attribute): JsonResponse
    {
        $rules = [
            'delete_image' => ['sometimes', 'boolean'],
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = ['required'];
        } elseif ($request->filled('image')) {
            $rules['image'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        $updatedAttribute = $this->repository->update($attribute, $validated);

        return successResponse(
            message: translate('cms.messages.image_updated_successfully'),
            data: resolve(AttributeResource::class, ['resource' => $updatedAttribute])->withFields(request()->get('fields')),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute): JsonResponse
    {
        $this->repository->delete($attribute);
        return successResponse(message: translate('cms.messages.deleted_successfully'));
    }
}
