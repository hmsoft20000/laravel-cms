<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Content\Service;
use HMsoft\Cms\Repositories\Contracts\ServiceRepositoryInterface;
use HMsoft\Cms\Traits\Attributes\HandlesAttributeSyncing;
use HMsoft\Cms\Traits\Categories\HandlesCategorySyncing;
use HMsoft\Cms\Traits\Downloads\HandlesDownloadSyncing;
use HMsoft\Cms\Traits\Faqs\HandlesFaqSyncing;
use HMsoft\Cms\Traits\Features\HandlesFeatureSyncing;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use HMsoft\Cms\Traits\Organizations\HandlesOrganizationSyncing;
use HMsoft\Cms\Traits\Plans\HandlesPlanSyncing;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ServiceRepository implements ServiceRepositoryInterface
{

    use
        FileManagerTrait,
        HandlesCategorySyncing,
        HandlesFeatureSyncing,
        HandlesDownloadSyncing,
        HandlesAttributeSyncing,
        HasTranslations,
        HandlesOrganizationSyncing,
        HandlesPlanSyncing,
        HandlesFaqSyncing;

    public function __construct(private readonly Service $model)
    {
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            /** @var Service $service */
            $service = $this->model->create(Arr::except($data, [
                'locales',
                'category_ids',
                'features',
                'downloads',
                'plans',
                'faqs',
                'attribute_values',
                'partner_ids',
                'sponsor_ids'
            ]));

            $this->syncTranslations($service, $data['locales'] ?? null);
            $this->syncCategories($service, $data['category_ids'] ?? null);
            $this->syncFeatures($service, $data['features'] ?? null);
            $this->syncPlans($service, $data['plans'] ?? null);
            $this->syncFaqs($service, $data['faqs'] ?? null);
            $this->syncDownloads($service, $data['downloads'] ?? null);
            $this->syncAttributeValues($service, $data['attribute_values'] ?? null);
            $this->syncOrganizations($service, $data);

            return $this->show($service);
        });
    }

    public function update(Model $service, array $data): Model
    {
        return DB::transaction(function () use ($service, $data) {
            /** @var Service $service */
            $service->update(Arr::except($data, [
                'locales',
                'category_ids',
                'features',
                'downloads',
                'plans',
                'faqs',
                'attribute_values',
                'partner_ids',
                'sponsor_ids'
            ]));

            $this->syncTranslations($service, $data['locales'] ?? null);
            $this->syncCategories($service, $data['category_ids'] ?? null);
            $this->syncFeatures($service, $data['features'] ?? null);
            $this->syncPlans($service, $data['plans'] ?? null);
            $this->syncFaqs($service, $data['faqs'] ?? null);
            $this->syncDownloads($service, $data['downloads'] ?? null);
            $this->syncAttributeValues($service, $data['attribute_values'] ?? null);
            $this->syncOrganizations($service, $data);

            return $this->show($service->fresh());
        });
    }

    public function show(Model $model): Model
    {
        return $model->load([
            'translations',
            'media',
            'categories.translations',
            // 'plans.owner',
            // 'plans.translations',
            // 'plans.features.translations',
            // 'features.owner',
            'features.translations',
            'downloads.translations',
            'downloads.categories',
            'downloads.media',
            'downloads.links',

            'partners.translations',
            'sponsors.translations',
            'attributeValues.attribute.translations',
            'attributeValues.attribute.options.translations',
            'attributeValues.selectedOptions.option.translations',
            'categories.translations',
        ]);
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function attachDownloads(Service $service, array $downloadItemIds): void
    {
        $service->downloads()->sync($downloadItemIds);
    }

    public function destroy(Model $model): bool
    {
        return $model->delete();
    }

    public function updateAll(array $servicesData): array
    {
        $updatedServices = [];
        foreach ($servicesData as $serviceData) {
            if ($serviceId = $serviceData['id'] ?? null) {
                $service = $this->model->findOrFail($serviceId);
                $updatedServices[] = $this->update($service, $serviceData);
            }
        }
        return $updatedServices;
    }
}
