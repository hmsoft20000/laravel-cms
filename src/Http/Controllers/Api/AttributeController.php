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
            model: new Attribute(),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($scope) {
                $query->ofScope($scope);
                $query->with(['translations', 'options.translations', 'categories']);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return (new AttributeResource($item))->withFields(request()->get('fields'));
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
            message: translate('cms::messages.added_successfully'),
            data: new AttributeResource($this->repository->show($attribute)),
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute): JsonResponse
    {
        return successResponse(
            data: new AttributeResource($this->repository->show($attribute))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute): JsonResponse
    {
        $updatedAttribute = $this->repository->update($attribute, $request->validated());
        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: new AttributeResource($updatedAttribute)
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
            message: translate('cms::messages.updated_successfully'),
            data: AttributeResource::collection($updatedAttributes)
        );
    }

    public function updateImage(Request $request, Attribute $attribute): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['sometimes', 'image', 'max:2048'],
            'delete_image' => ['sometimes'],
        ]);

        $updatedFeature = $this->repository->update($attribute, $validated);

        return successResponse(
            message: translate('cms::messages.image_updated_successfully'),
            data: new AttributeResource($updatedFeature)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute): JsonResponse
    {
        $this->repository->delete($attribute);
        return successResponse(message: translate('cms::messages.deleted_successfully'));
    }
}
