<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Shared\Category;
use HMsoft\Cms\Repositories\Contracts\CategoryRepositoryInterface;
use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use HMsoft\Cms\Traits\Media\HandlesSingleMedia;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CategoryRepository implements CategoryRepositoryInterface
{
    use HandlesSingleMedia, HasTranslations;

    /**
     * The storage path for category images.
     * @var string
     */
    private string $folder = 'categories';

    public function __construct(
        private readonly Category $model,
        private readonly MediaRepositoryInterface $mediaRepository,
    ) {}


    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $model = $this->model->create(Arr::except($data, ['locales', 'image', 'delete_image']));

            $this->syncSingleImage($model, $data, $this->mediaRepository);

            $this->syncTranslations($model, $data['locales'] ?? null);

            return $this->show($model->refresh());
        });
    }

    public function update(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {

            $model->update(Arr::except($data, ['locales', 'type', 'delete_image', 'image', 'delete_image']));

            $this->syncTranslations($model, $data['locales'] ?? null);
            $this->syncSingleImage($model, $data, $this->mediaRepository);

            return $this->show($model->refresh());
        });
    }

    public function show(Model $model): Model
    {
        return $model->load('translations');
    }

    public function delete(Model $model): bool
    {
        return $this->destroy($model);
    }

    public function destroy(Model $model): bool
    {
        $model->delete();
        return true;
    }
}
