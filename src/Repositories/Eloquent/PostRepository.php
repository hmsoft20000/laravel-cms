<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Content\Post;
use HMsoft\Cms\Repositories\Contracts\PostRepositoryInterface;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use HMsoft\Cms\Traits\Categories\HandlesCategorySyncing;
use HMsoft\Cms\Traits\Features\HandlesFeatureSyncing;
use HMsoft\Cms\Traits\Downloads\HandlesDownloadSyncing;
use HMsoft\Cms\Traits\Keywords\HandlesKeywordSyncing;
use HMsoft\Cms\Traits\Attributes\HandlesAttributeSyncing;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use HMsoft\Cms\Traits\Faqs\HandlesFaqSyncing;
use HMsoft\Cms\Traits\Organizations\HandlesOrganizationSyncing;
use HMsoft\Cms\Traits\Plans\HandlesPlanSyncing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PostRepository implements PostRepositoryInterface
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
        HandlesFaqSyncing,
        HandlesAttributeSyncing;

    public function __construct(private readonly Post $model) {}

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            /** @var Post $post */
            $post = $this->model->create(Arr::except($data, [
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

            $this->syncTranslations($post, $data['locales'] ?? null);
            $this->syncCategories($post, $data['category_ids'] ?? null);
            $this->syncFeatures($post, $data['features'] ?? null);
            $this->syncPlans($post, $data['plans'] ?? null);
            $this->syncFaqs($post, $data['faqs'] ?? null);
            $this->syncDownloads($post, $data['downloads'] ?? null);
            $this->syncKeywords($post, $data['keywords'] ?? null);
            $this->syncAttributeValues($post, $data['attribute_values'] ?? null);
            $this->syncOrganizations($post, $data);

            return $this->show($post);
        });
    }

    public function update(Model $post, array $data): Model
    {
        return DB::transaction(function () use ($post, $data) {
            /** @var Post $post */
            $post->update(Arr::except($data, [
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

            $this->syncTranslations($post, $data['locales'] ?? null);
            $this->syncCategories($post, $data['category_ids'] ?? null);
            $this->syncFeatures($post, $data['features'] ?? null);
            $this->syncPlans($post, $data['plans'] ?? null);
            $this->syncFaqs($post, $data['faqs'] ?? null);
            $this->syncDownloads($post, $data['downloads'] ?? null);
            $this->syncKeywords($post, $data['keywords'] ?? null);
            $this->syncAttributeValues($post, $data['attribute_values'] ?? null);
            $this->syncOrganizations($post, $data);

            return $this->show($post->fresh());
        });
    }

    public function show(Model $model): Model
    {
        // This load structure is based on your original file for maximum compatibility
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
        // The logic for deleting associated files should be placed in the Post model's 'deleting' event.
        // This ensures that file cleanup occurs regardless of how the model is deleted.
        return $model->delete();
    }

    public function destroy(Model $model): bool
    {
        return $model->delete();
    }

    public function updateAll(array $postsData): array
    {
        $updatedPosts = [];
        foreach ($postsData as $postData) {
            if ($postId = $postData['id'] ?? null) {
                $post = $this->model->findOrFail($postId);
                $updatedPosts[] = $this->update($post, $postData);
            }
        }
        return $updatedPosts;
    }
}
