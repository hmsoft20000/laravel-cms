<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Content\Blog;
use HMsoft\Cms\Repositories\Contracts\BlogRepositoryInterface;
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

class BlogRepository implements BlogRepositoryInterface
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

    public function __construct(private readonly Blog $model)
    {
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            /** @var Blog $blog */
            $blog = $this->model->create(Arr::except($data, [
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

            $this->syncTranslations($blog, $data['locales'] ?? null);
            $this->syncCategories($blog, $data['category_ids'] ?? null);
            $this->syncFeatures($blog, $data['features'] ?? null);
            $this->syncPlans($blog, $data['plans'] ?? null);
            $this->syncFaqs($blog, $data['faqs'] ?? null);
            $this->syncDownloads($blog, $data['downloads'] ?? null);
            $this->syncKeywords($blog, $data['keywords'] ?? null);
            $this->syncAttributeValues($blog,$data['attribute_values'] ?? null);
            $this->syncOrganizations($blog, $data);

            return $this->show($blog);
        });
    }

    public function update(Model $blog, array $data): Model
    {
        return DB::transaction(function () use ($blog, $data) {
            /** @var Blog $blog */
            $blog->update(Arr::except($data, [
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

            $this->syncTranslations($blog, $data['locales'] ?? null);
            $this->syncCategories($blog, $data['category_ids'] ?? null);
            $this->syncFeatures($blog, $data['features'] ?? null);
            $this->syncPlans($blog, $data['plans'] ?? null);
            $this->syncFaqs($blog, $data['faqs'] ?? null);
            $this->syncDownloads($blog, $data['downloads'] ?? null);
            $this->syncKeywords($blog, $data['keywords'] ?? null);
            $this->syncAttributeValues($blog, $data['attribute_values'] ?? null);
            $this->syncOrganizations($blog, $data);

            return $this->show($blog->fresh());
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

    public function attachDownloads(Blog $blog, array $downloadItemIds): void
    {
        $blog->downloads()->sync($downloadItemIds);
    }

    public function destroy(Model $model): bool
    {
        return $model->delete();
    }

    public function updateAll(array $blogsData): array
    {
        $updatedBlogs = [];
        foreach ($blogsData as $blogData) {
            if ($blogId = $blogData['id'] ?? null) {
                $blog = $this->model->findOrFail($blogId);
                $updatedBlogs[] = $this->update($blog, $blogData);
            }
        }
        return $updatedBlogs;
    }
}
