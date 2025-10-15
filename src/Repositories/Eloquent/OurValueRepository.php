<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\OurValue\OurValue;
use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use HMsoft\Cms\Repositories\Contracts\OurValueRepositoryInterface;
use HMsoft\Cms\Traits\Media\HandlesSingleMedia;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class OurValueRepository  implements OurValueRepositoryInterface
{
    use HasTranslations, HandlesSingleMedia;

    public function __construct(
        private readonly OurValue $model,
        private readonly MediaRepositoryInterface $mediaRepository
    ) {}

    public function store(array $data): Model
    {
        $createData = Arr::except($data, ['locales', 'image', 'delete_image']);

        $model = $this->model->create($createData);
        $this->syncTranslations($model, $data['locales'] ?? null);
        $this->syncSingleImage($model, $data, $this->mediaRepository);
        return $this->show($model);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update(Arr::except($data, ['locales', 'image', 'delete_image']));
        $this->syncTranslations($model, $data['locales'] ?? null);
        $this->syncSingleImage($model, $data, $this->mediaRepository);
        return $this->show($model->refresh());
    }

    public function show(Model $model): Model
    {
        return $model->load(['translations', 'image']);
    }

    public function delete(Model $model): bool
    {
        $model->delete();
        return true;
    }

    public function destroy(Model $model): bool
    {
        $model->forceDelete();
        return true;
    }
}
