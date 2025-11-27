<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Content\Portfolio;
use HMsoft\Cms\Repositories\Contracts\PortfolioRepositoryInterface;
use HMsoft\Cms\Traits\Attributes\HandlesAttributeSyncing;
use HMsoft\Cms\Traits\Categories\HandlesCategorySyncing;
use HMsoft\Cms\Traits\Downloads\HandlesDownloadSyncing;
use HMsoft\Cms\Traits\Faqs\HandlesFaqSyncing;
use HMsoft\Cms\Traits\Features\HandlesFeatureSyncing;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use HMsoft\Cms\Traits\Keywords\HandlesKeywordSyncing;
use HMsoft\Cms\Traits\Organizations\HandlesOrganizationSyncing;
use HMsoft\Cms\Traits\Plans\HandlesPlanSyncing;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PortfolioRepository implements PortfolioRepositoryInterface
{

    use
        FileManagerTrait,
        HandlesCategorySyncing,
        HandlesFeatureSyncing,
        HandlesDownloadSyncing,
        HandlesKeywordSyncing,
        HandlesAttributeSyncing,
        HasTranslations,
        HandlesOrganizationSyncing,
        HandlesPlanSyncing,
        HandlesFaqSyncing;

    public function __construct(private readonly Portfolio $model)
    {
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            /** @var Portfolio $portfolio */
            $portfolio = $this->model->create(Arr::except($data, [
                'locales',
                'category_ids',
                'features',
                'downloads',
                'plans',
                'faqs',
                'keywords',
                'attribute_values',
                'partner_ids',
                'sponsor_ids'
            ]));

            $this->syncTranslations($portfolio, $data['locales'] ?? null);
            $this->syncCategories($portfolio, $data['category_ids'] ?? null);
            $this->syncFeatures($portfolio, $data['features'] ?? null);
            $this->syncPlans($portfolio, $data['plans'] ?? null);
            $this->syncFaqs($portfolio, $data['faqs'] ?? null);
            $this->syncDownloads($portfolio, $data['downloads'] ?? null);
            $this->syncKeywords($portfolio, $data['keywords'] ?? null);
            $this->syncAttributeValues($portfolio, $data['attribute_values'] ?? null);
            $this->syncOrganizations($portfolio, $data);

            return $this->show($portfolio);
        });
    }

    public function update(Model $portfolio, array $data): Model
    {
        return DB::transaction(function () use ($portfolio, $data) {
            /** @var Portfolio $portfolio */
            $portfolio->update(Arr::except($data, [
                'locales',
                'category_ids',
                'features',
                'downloads',
                'plans',
                'faqs',
                'keywords',
                'attribute_values',
                'partner_ids',
                'sponsor_ids'
            ]));

            $this->syncTranslations($portfolio, $data['locales'] ?? null);
            $this->syncCategories($portfolio, $data['category_ids'] ?? null);
            $this->syncFeatures($portfolio, $data['features'] ?? null);
            $this->syncPlans($portfolio, $data['plans'] ?? null);
            $this->syncFaqs($portfolio, $data['faqs'] ?? null);
            $this->syncDownloads($portfolio, $data['downloads'] ?? null);
            $this->syncKeywords($portfolio, $data['keywords'] ?? null);
            $this->syncAttributeValues($portfolio, $data['attribute_values'] ?? null);
            $this->syncOrganizations($portfolio, $data);

            return $this->show($portfolio->fresh());
        });
    }

    public function show(Model $model): Model
    {
        return $model->load([
            'translations',
            'media',
            'keywords',
            'categories.translations',
            'plans.owner',
            'plans.translations',
            'plans.features.translations',
            'features.owner',
            'features.translations',
            'downloads.owner',
            'downloads.translations',
            'partners.translations',
            'sponsors.translations',
            'attributeValues.attribute.translations',
            'attributeValues.attribute.options.translations',
            'attributeValues.selectedOptions.option.translations',
        ]);
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function attachDownloads(Portfolio $portfolio, array $downloadItemIds): void
    {
        $portfolio->downloads()->sync($downloadItemIds);
    }

    public function destroy(Model $model): bool
    {
        return $model->delete();
    }

    public function updateAll(array $portfoliosData): array
    {
        $updatedPortfolios = [];
        foreach ($portfoliosData as $portfolioData) {
            if ($portfolioId = $portfolioData['id'] ?? null) {
                $portfolio = $this->model->findOrFail($portfolioId);
                $updatedPortfolios[] = $this->update($portfolio, $portfolioData);
            }
        }
        return $updatedPortfolios;
    }
}
