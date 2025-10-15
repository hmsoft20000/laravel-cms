<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Faqs\{StoreFaqRequest, UpdateAllFaqRequest, UpdateFaqRequest};
use HMsoft\Cms\Http\Resources\Api\FaqResource;
use HMsoft\Cms\Models\Shared\Faq;
use HMsoft\Cms\Repositories\Contracts\FaqRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FaqController extends Controller
{


    public function __construct(
        private readonly FaqRepositoryInterface $repository
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
            model: new Faq(),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($owner) {
                $query->where('owner_type', $owner->getMorphClass());
                $query->where('owner_id', $owner->id);
                $query->with([
                    'translations',
                ]);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return new FaqResource($item);
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created resource in storage and attach it to the owner model.
     * @param StoreFaqRequest $request
     * @param Model $owner
     * @return JsonResponse
     */
    public function store(StoreFaqRequest $request, Model $owner): JsonResponse
    {
        // $this->authorize('create', Feature::class);
        $validated = $request->validated();
        $ownerData = [
            'owner_id' => $owner->id,
            'owner_type' => $owner->getMorphClass(),
        ];
        $validated = array_merge($validated, $ownerData);
        $faq = $this->repository->store($validated);
        return successResponse(
            message: translate('cms::messages.added_successfully'),
            data: new FaqResource($faq),
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource and check if it belongs to the owner model.
     * @param Model $owner
     * @param Faq $faq
     * @return JsonResponse
     */
    public function show(Model $owner, Faq $faq): JsonResponse
    {
        // $this->authorize('view', $faq);

        // Optional: Add a check to ensure the plan belongs to the correct type
        if ($faq->owner_type != $owner->getMorphClass() || $faq->owner_id != $owner->id) {
            abort(404);
        }
        return successResponse(data: new FaqResource($this->repository->show($faq)));
    }

    /**
     * Update the specified resource in storage and check if it belongs to the owner model.
     *
     * @param UpdateFaqRequest $request
     * @param Model $owner
     * @param Faq $faq
     * @return JsonResponse
     */
    public function update(UpdateFaqRequest $request, Model $owner, Faq $faq): JsonResponse
    {
        // $this->authorize('update', $feature);

        // Optional: Add a check to ensure the feature belongs to the correct type
        if ($faq->owner_type != $owner->getMorphClass() || $faq->owner_id != $owner->id) {
            abort(404);
        }
        $updatedFaq = $this->repository->update($faq, $request->validated());
        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: new FaqResource($updatedFaq)
        );
    }

    /**
     * Remove the specified resource from storage and check if it belongs to the owner model.
     *
     * @param Model $owner
     * @param Faq $faq
     * @return JsonResponse
     */
    public function destroy(Model $owner, Faq $faq): JsonResponse
    {
        // $this->authorize('delete', $feature);

        // Optional: Add a check to ensure the faq belongs to the correct type
        if ($faq->owner_type != $owner->getMorphClass() || $faq->owner_id != $owner->id) {
            abort(404);
        }
        $this->repository->delete($faq);
        return successResponse(message: translate('cms::messages.deleted_successfully'));
    }

    /**
     * Update all the faqs for the owner model.
     *
     * @param Request $request
     * @param Model $owner
     * @return JsonResponse
     */
    public function updateAll(UpdateAllFaqRequest $request, Model $owner): JsonResponse
    {
        // $this->authorize('bulkUpdate', Faq::class);

        $updatedFaqs = [];
        foreach ($request->all() as $faqData) {
            if (isset($faqData['id'])) {
                $faq = Faq::findOrFail($faqData['id']);
                if ($faq->owner_type != $owner->getMorphClass() || $faq->owner_id != $owner->id) {
                    abort(404);
                }
                $updatedFaqs[] = $this->repository->update($faq, $faqData);
            }
        }

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: FaqResource::collection($updatedFaqs)
        );
    }
}
