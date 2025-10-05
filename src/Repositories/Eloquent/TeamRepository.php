<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Team\Team;
use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use HMsoft\Cms\Repositories\Contracts\TeamRepositoryInterface;
use HMsoft\Cms\Traits\Media\HandlesSingleMedia;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TeamRepository implements TeamRepositoryInterface
{
    use HasTranslations, HandlesSingleMedia;

    public function __construct(
        private readonly Team $model,
        private readonly MediaRepositoryInterface $mediaRepository,
    ) {}

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $createData = Arr::except($data, ['locales', 'image', 'delete_image']);
            $model = $this->model->create($createData);

            $this->syncSingleImage($model, $data, $this->mediaRepository);
            $this->syncTranslations($model, $data['locales'] ?? null);

            return $model;
        });
    }

    public function update(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {

            $model->update(Arr::except($data, ['locales', 'image', 'delete_image']));

            $this->syncTranslations($model, $data['locales'] ?? null);

            $this->syncSingleImage($model, $data, $this->mediaRepository);

            return $model->refresh();
        });
    }

    public function show(Model $model): Model
    {
        return $model;
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
