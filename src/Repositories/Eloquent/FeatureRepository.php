<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Shared\Feature;
use HMsoft\Cms\Repositories\Contracts\FeatureRepositoryInterface;
use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use HMsoft\Cms\Traits\Media\HandlesSingleMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class FeatureRepository implements FeatureRepositoryInterface
{
    use HasTranslations, HandlesSingleMedia;

    public function __construct(private readonly Feature $model, private MediaRepositoryInterface $mediaRepository) {}

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            // Get the full model class name from the morphMap
            $ownerType = Relation::getMorphedModel($data['owner_type']);

            // Check if the alias is valid
            if (!$ownerType || !class_exists($ownerType)) {
                throw new \InvalidArgumentException("Invalid owner type alias: '{$data['owner_type']}'");
            }

            $feature = $this->model->create(Arr::except($data, ['locales']));

            $this->syncSingleImage($feature, $data, $this->mediaRepository);
            $this->syncTranslations($feature, $data['locales'] ?? null);

            return $this->show($feature);
        });
    }


    public function update(Model $feature, array $data): Model
    {
        return DB::transaction(function () use ($feature, $data) {
            // Update the model with the main data, excluding image and locale fields
            $feature->update(Arr::except($data, ['locales', 'image', 'delete_image']));

            $this->syncSingleImage($feature, $data, $this->mediaRepository);

            $this->syncTranslations($feature, $data['locales'] ?? null);

            return $this->show($feature->refresh());
        });
    }

    public function show(Model $model): Model
    {
        return $model->load('translations');
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
