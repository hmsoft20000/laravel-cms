<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Contracts\AuthServiceInterface;
use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Portfolio\{StorePortfolioRequest, UpdatePortfolioRequest, UpdateAllPortfolioRequest};
use HMsoft\Cms\Http\Resources\Api\PortfolioResource;
use HMsoft\Cms\Models\Content\Portfolio;
use HMsoft\Cms\Repositories\Contracts\PortfolioRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;

class PortfolioController extends Controller
{
    protected AuthServiceInterface $authService;

    public function __construct(
        private readonly PortfolioRepositoryInterface $repo
    ) {
        $this->authService = app(AuthServiceInterface::class);
    }
    /**
     * Display a listing of the portfolios.
     */
    public function index(): JsonResponse
    {
        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: new Portfolio(),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) {
                // if (!$this->authService->hasPermission('portfolios.viewUnpublished')) {
                //     $query->where('t_main.is_active', true);
                // }

                $query->with([
                    'translations',
                    'media',
                    'keywords',
                    'categories.translations',
                    'features.translations',
                    'downloads.translations',
                    'partners.translations',
                    'sponsors.translations',
                    'attributeValues.attribute.translations',
                    'attributeValues.attribute.options.translations',
                    'attributeValues.selectedOptions.option.translations',
                ]);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return (new PortfolioResource($item))->withFields(request()->get('fields'));
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created portfolio in storage.
     */
    public function store(StorePortfolioRequest $request): JsonResponse
    {
        $portfolio = $this->repo->store($request->validated());
        return successResponse(
            message: translate('cms::messages.added_successfully'),
            data: (new PortfolioResource($this->repo->show($portfolio)))->withFields(request()->get('fields')),
        );
    }

    /**
     * Display the specified portfolio.
     */
    public function show(Portfolio $portfolio): JsonResponse
    {
        $portfolio = $this->repo->show($portfolio);
        return successResponse(data: (new PortfolioResource($portfolio))->withFields(request()->get('fields')));
    }

    /**
     * Update the specified portfolio in storage.
     */
    public function update(UpdatePortfolioRequest $request, Portfolio $portfolio): JsonResponse
    {
        $updatedPortfolio = $this->repo->update($portfolio, $request->validated());

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: (new PortfolioResource($updatedPortfolio))->withFields(request()->get('fields'))
        );
    }

    public function updateAll(UpdateAllPortfolioRequest $request): JsonResponse
    {
        $updatedPortfolios = [];
        foreach ($request->all() as $portfolioData) {
            if (isset($portfolioData['id'])) {
                $portfolio = Portfolio::findOrFail($portfolioData['id']);
                $updatedPortfolios[] = $this->repo->update($portfolio, $portfolioData);
            }
        }

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: PortfolioResource::collection($updatedPortfolios)
        );
    }

    /**
     * Remove the specified portfolio from storage.
     */
    public function destroy(Portfolio $portfolio): JsonResponse
    {
        $this->repo->delete($portfolio);
        return successResponse(
            message: translate('cms::messages.deleted_successfully'),
        );
    }
}
