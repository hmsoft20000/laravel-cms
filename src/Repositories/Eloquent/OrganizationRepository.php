<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Organizations\Organization;
use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use HMsoft\Cms\Repositories\Contracts\OrganizationRepositoryInterface;
use HMsoft\Cms\Traits\Media\HandlesSingleMedia;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    use HasTranslations, HandlesSingleMedia;

    public function __construct(
        private readonly Organization $model,
        private readonly MediaRepositoryInterface $mediaRepository,
    ) {}

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $createData = Arr::except($data, ['locales', 'image', 'delete_image']);

            if (array_key_exists('sort_number', $data) && !isset($createData['sort_number'])) {
                $createData['sort_number'] = $data['sort_number'];
            } else {
                $createData['sort_number'] = $this->model->max('sort_number') + 1;
            }

            $model = $this->model->create($createData);

            $this->syncTranslations($model, $data['locales'] ?? null);

            $this->syncSingleImage($model, $data, $this->mediaRepository);

            return $this->show($model);
        });
    }

    public function update(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {

            $model->update(Arr::except($data, ['locales', 'image', 'delete_image']));

            $this->syncTranslations($model, $data['locales'] ?? null);

            $this->syncSingleImage($model, $data, $this->mediaRepository);


            return $this->show($model);
        });
    }

    public function show(Model $model): Model
    {
        return $model->load('translations');
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
