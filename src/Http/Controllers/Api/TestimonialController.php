<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Testimonial\{Store, Update, Delete, UpdateAll};
use HMsoft\Cms\Http\Resources\Api\TestimonialResource;
use HMsoft\Cms\Models\Testimonial\Testimonial;
use HMsoft\Cms\Repositories\Contracts\TestimonialRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{

    public function __construct(
        private readonly TestimonialRepositoryInterface  $repo,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', Testimonial::class);

        $result =  AutoFilterAndSortService::dynamicSearchFromRequest(
            model: Testimonial::class,
        );

        $result['data'] =  collect($result['data'])->map(function ($item) {
            return resolve(TestimonialResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    public function show(Testimonial $testimonial)
    {
        // $this->authorize('view', $testimonial);

        $this->repo->show($testimonial);
        return  successResponse(
            data: resolve(TestimonialResource::class, ['resource' => $testimonial])->withFields(request()->get('fields'))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request)
    {
        // $this->authorize('create', Testimonial::class);

        $validated = $request->validated();
        $testimonial = $this->repo->store($validated);
        return  successResponse(
            message: translate('cms.messages.added_successfully'),
            data: resolve(TestimonialResource::class, ['resource' => $testimonial])->withFields(request()->get('fields'))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, Testimonial $testimonial)
    {
        // $this->authorize('update', $testimonial);

        $validated = $request->validated();
        $testimonial = $this->repo->update($testimonial, $validated);
        return  successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(TestimonialResource::class, ['resource' => $testimonial])->withFields(request()->get('fields'))
        );
    }

    public function updateAll(UpdateAll $request)
    {
        // $this->authorize('updateAny', Testimonial::class);

        $updatedTestimonials = [];
        foreach ($request->all() as $testimonialData) {
            if (isset($testimonialData['id'])) {
                $testimonial = Testimonial::findOrFail($testimonialData['id']);
                // $this->authorize('update', $testimonial);
                $updatedTestimonials[] = $this->repo->update($testimonial, $testimonialData);
            }
        }
        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: collect($updatedTestimonials)->map(function ($item) {
                return resolve(TestimonialResource::class, ['resource' => $item])->withFields(request()->get('fields'));
            })->all(),
        );
    }

    public function updateImage(Request $request, Testimonial $testimonial): JsonResponse
    {
        // $this->authorize('manageImages', $testimonial);

        $validated = $request->validate([
            'image' => ['required', 'image', 'max:2048'],
        ]);

        $updatedTestimonial = $this->repo->update($testimonial, $validated);

        return successResponse(
            message: translate('cms.messages.image_updated_successfully'),
            data: resolve(TestimonialResource::class, ['resource' => $updatedTestimonial])->withFields(request()->get('fields')),
        );
    }

    public function destroy(Delete $request, Testimonial $testimonial)
    {
        // $this->authorize('delete', $testimonial);

        $this->repo->destroy($testimonial);
        return  successResponse(
            message: translate('cms.messages.deleted_successfully'),
        );
    }
}
