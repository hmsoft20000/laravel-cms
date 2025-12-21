<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Shop\Item;
use HMsoft\Cms\Repositories\Contracts\ItemRepositoryInterface;
use HMsoft\Cms\Traits\Attributes\HandlesAttributeSyncing;
use HMsoft\Cms\Traits\Categories\HandlesCategorySyncing;
use HMsoft\Cms\Traits\Downloads\HandlesDownloadSyncing;
use HMsoft\Cms\Traits\Faqs\HandlesFaqSyncing;
use HMsoft\Cms\Traits\Features\HandlesFeatureSyncing;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use HMsoft\Cms\Traits\Organizations\HandlesOrganizationSyncing;
use HMsoft\Cms\Traits\Plans\HandlesPlanSyncing;
use HMsoft\Cms\Traits\Translations\HasTranslations;

use HMsoft\Cms\Traits\Shop\HandlesAddonSyncing;
use HMsoft\Cms\Traits\Shop\HandlesVariationSyncing;
use HMsoft\Cms\Traits\Shop\HandlesItemJoinSyncing;
use HMsoft\Cms\Traits\Shop\HandlesItemRelationshipSyncing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ItemRepository implements ItemRepositoryInterface
{
    // Include all relevant traits from your package
    use
        FileManagerTrait,
        HandlesCategorySyncing,
        HandlesFeatureSyncing,
        HandlesDownloadSyncing,
        HandlesAttributeSyncing,
        HasTranslations,
        HandlesOrganizationSyncing,
        HandlesPlanSyncing,
        HandlesFaqSyncing,
        HandlesAddonSyncing,
        HandlesVariationSyncing,
        HandlesItemJoinSyncing,
        HandlesItemRelationshipSyncing;

    // You will need to CREATE these new traits to handle syncing for your new relations
    // use HandlesVariationSyncing, HandlesAddonSyncing, HandlesItemJoinSyncing, HandlesItemRelationshipSyncing;

    public function __construct(private readonly Item $model) {}

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            /** @var Item $item */
            $item = $this->model->create(Arr::except($data, [
                'locales',
                'category_ids',
                'features',
                'downloads',
                'plans',
                'faqs',
                'attribute_values',
                'partner_ids',
                'sponsor_ids',
                'variations',
                'addons',
                'joins',
                'relationships',
                'images',
                'attached_blogs_ids'
            ]));

            // Use existing sync traits
            $this->syncTranslations($item, $data['locales'] ?? null);
            $this->syncCategories($item, $data['category_ids'] ?? null);
            $this->syncFeatures($item, $data['features'] ?? null);
            $this->syncPlans($item, $data['plans'] ?? null);
            $this->syncFaqs($item, $data['faqs'] ?? null);
            $this->syncDownloads($item, $data['downloads'] ?? null);
            $this->syncOrganizations($item, $data); // For partners/sponsors
            $this->syncAttributeValues($item, $data['attribute_values'] ?? null);

            $this->syncVariations($item, $data['variations'] ?? null);
            $this->syncAddons($item, $data['addons'] ?? null);
            $this->syncJoins($item, $data['joins'] ?? null);
            $this->syncRelationships($item, $data['relationships'] ?? null);
            // attached_download_ids
            $this->attachDownloads($item, $data['attached_download_ids'] ?? []);
            if (isset($data['attached_blogs_ids'])) {
                $item->syncBlogs($data['attached_blogs_ids']);
            }
            return $this->show($item);
        });
    }

    public function update(Model $item, array $data): Model
    {
        return DB::transaction(function () use ($item, $data) {
            /** @var Item $item */
            $item->update(Arr::except($data, [
                'locales',
                'category_ids',
                'features',
                'downloads',
                'plans',
                'faqs',
                'attribute_values',
                'partner_ids',
                'sponsor_ids',
                'variations',
                'addons',
                'joins',
                'relationships',
                'images'
            ]));

            // Use existing sync traits
            $this->syncTranslations($item, $data['locales'] ?? null);
            $this->syncCategories($item, $data['category_ids'] ?? null);
            $this->syncFeatures($item, $data['features'] ?? null);
            $this->syncPlans($item, $data['plans'] ?? null);
            $this->syncFaqs($item, $data['faqs'] ?? null);
            $this->syncDownloads($item, $data['downloads'] ?? null);
            $this->syncAttributeValues($item, $data['attribute_values'] ?? null);
            $this->syncOrganizations($item, $data);
            $this->attachDownloads($item, $data['attached_download_ids'] ?? []);
            $this->syncVariations($item, $data['variations'] ?? null);
            $this->syncAddons($item, $data['addons'] ?? null);
            $this->syncJoins($item, $data['joins'] ?? null);
            $this->syncRelationships($item, $data['relationships'] ?? null);
            if (isset($data['attached_blogs_ids'])) {
                $item->syncBlogs($data['attached_blogs_ids']);
            }
            return $this->show($item->fresh());
        });
    }

    public function show(Model $model): Model
    {
        // Load all necessary relations for a single item view
        return $model->load([
            'translations',
            'media',
            'categories.translations',
            'plans.translations',
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
            'variations.attributeOptions.translations', // Load variation data
            'addons.translations', // Load addon data
            'addons.options.translations', // Load addon option data
            'childItems.item.translations', // Load bundled item data
            'relationships.relatedItem.translations', // Load related item data
            'blogs.translations',
        ]);
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function destroy(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * @param Item $item 
     * @param array $downloadItemIds
     * @return void
     */
    public function attachDownloads(Item $item, array $downloadItemIds): void
    {
        $item->downloads()->sync($downloadItemIds);
    }
}
