<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Shared\Attribute;
use HMsoft\Cms\Repositories\Contracts\AttributeRepositoryInterface;
use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use HMsoft\Cms\Traits\Media\HandlesSingleMedia;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AttributeRepository implements AttributeRepositoryInterface
{
    use HandlesSingleMedia, HasTranslations;

    private string $folder = 'attributes';

    public function __construct(private readonly Attribute $model, private readonly MediaRepositoryInterface $mediaRepository) {}

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $attribute = $this->model->create(Arr::except($data, ['locales', 'options', 'category_ids', 'image']));

            if (isset($data['category_ids'])) {
                $attribute->categories()->sync($data['category_ids']);
            }

            $this->syncTranslations($attribute, $data['locales'] ?? null);

            $this->syncSingleImage($attribute, $data, $this->mediaRepository);

            if (in_array($data['type'], ['select', 'radio', 'checkbox']) && !empty($data['options'])) {
                foreach ($data['options'] as $optionData) {
                    $option = $attribute->options()->create(Arr::except($optionData, ['locales']));
                    $this->syncTranslations($option, $optionData['locales'] ?? null);
                }
            }
            return $this->show($attribute);
        });
    }

    public function update(Model $attribute, array $data): Model
    {
        return DB::transaction(function () use ($attribute, $data) {


            /** @var Attribute $attribute */
            $attribute->update(Arr::except($data, ['locales', 'options', 'category_ids', 'scope', 'image']));

            if (Arr::has($data, 'category_ids')) {
                $attribute->categories()->sync($data['category_ids'] ?? []);
            }

            $this->syncTranslations($attribute, $data['locales'] ?? null);

            $this->syncSingleImage($attribute, $data, $this->mediaRepository);

            if (isset($data['options']) && in_array($attribute->type, ['select', 'radio', 'checkbox'])) {
                $this->syncOptions($attribute, $data['options'] ?? null);
            }

            if (isset($data['type']) && !in_array($data['type'], ['select', 'radio', 'checkbox'])) {
                $attribute->options()->delete();
            }

            return $this->show($attribute->fresh());
        });
    }

    public function show(Model $model): Model
    {
        return $model->load(['translations', 'options.translations', 'categories']);
    }

    public function delete(Model $model): bool
    {
        return $this->destroy($model);
    }

    public function destroy(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            /** @var Attribute $model */
            $model->categories()->detach();
            return $model->delete();
        });
    }

    private function syncOptions(Attribute $attribute, ?array $optionsData = null): void
    {
        $existingOptionIds = $attribute->options()->pluck('id')->all();
        $incomingOptionIds = Arr::pluck(Arr::whereNotNull($optionsData, 'id'), 'id');
        $optionIdsToDelete = array_diff($existingOptionIds, $incomingOptionIds);

        if (!empty($optionIdsToDelete)) {
            $attribute->options()->whereIn('id', $optionIdsToDelete)->delete();
        }

        foreach ($optionsData ?? [] as $optionData) {
            $option = $attribute->options()->updateOrCreate(
                ['id' => $optionData['id'] ?? null],
                Arr::except($optionData, ['id', 'locales'])
            );

            $this->syncTranslations($option, $optionData['locales'] ?? null);
        }
    }
}
