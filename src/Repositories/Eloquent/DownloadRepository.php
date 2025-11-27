<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Shared\Download;
use HMsoft\Cms\Repositories\Contracts\DownloadRepositoryInterface;
use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use HMsoft\Cms\Traits\Media\HandlesSingleFile;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DownloadRepository implements DownloadRepositoryInterface
{
    use HandlesSingleFile, HasTranslations;

    public function __construct(private readonly Download $model, private readonly MediaRepositoryInterface $mediaRepository) {}


    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $model = $this->model->create(Arr::except($data, ['locales', 'file']));

            $this->syncTranslations($model, $data['locales'] ?? null);
            $this->syncSingleFile($model, $data, $this->mediaRepository);


            return $this->show($model->refresh());
        });
    }

    public function update(Model $download, array $data): Model
    {
        return DB::transaction(function () use ($download, $data) {
            $download->update(Arr::except($data, ['locales', 'file', 'delete_file']));
            $this->syncTranslations($download, $data['locales'] ?? null);
            $this->syncSingleFile($download, $data, $this->mediaRepository);
            return $this->show($download->refresh());
        });
    }

    public function show(Model $model): Model
    {
        return $model->load('downloadItem', 'downloadItem.translations', 'downloadItem.links');
    }

    public function delete(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            return $model->delete();
        });
    }

    public function destroy(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            return $model->forceDelete();
        });
    }
}
