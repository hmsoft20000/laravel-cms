<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Shared\DownloadItem;
use HMsoft\Cms\Repositories\Contracts\DownloadItemRepositoryInterface;
use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use HMsoft\Cms\Traits\Categories\HandlesCategorySyncing;
use HMsoft\Cms\Traits\Media\HandlesSingleMedia;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DownloadItemRepository implements DownloadItemRepositoryInterface
{

    use HandlesSingleMedia, HasTranslations, HandlesCategorySyncing;


    public function __construct(
        private readonly DownloadItem $model,
        private readonly MediaRepositoryInterface $mediaRepository,
    ) {}


    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $model = $this->model->create(Arr::except($data, ['locales', 'image', 'delete_image', 'category_ids', 'download_links']));

            $this->syncSingleImage($model, $data, $this->mediaRepository);

            $this->syncTranslations($model, $data['locales'] ?? null);

            $this->syncCategories($model, $data['category_ids'] ?? null);

            foreach ($data['download_links'] ?? [] as $linkData) {
                $model->links()->updateOrCreate(
                    ['file_path' => $linkData['file_path']],
                    Arr::except($linkData, 'file_path')
                );
            }


            return $this->show($model->refresh());
        });
    }

    public function update(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {

            $model->update(Arr::except($data, ['locales', 'type', 'delete_image', 'image', 'delete_image', 'category_ids', 'download_links']));

            $this->syncTranslations($model, $data['locales'] ?? null);

            $this->syncSingleImage($model, $data, $this->mediaRepository);

            $this->syncCategories($model, $data['category_ids'] ?? null);

            $linksData = $data['download_links'] ?? null;

            if ($linksData != null) {
                $filePaths = collect($linksData)->pluck('file_path')->toArray();
                $model->links()
                    ->whereNotIn('file_path', $filePaths)
                    ->delete();


                foreach ($linksData ?? [] as $linkData) {
                    $model->links()->updateOrCreate(
                        ['file_path' => $linkData['file_path']],
                        Arr::except($linkData, 'file_path')
                    );
                }
            }

            return $this->show($model->refresh());
        });
    }

    public function show(Model $model): Model
    {
        return $model->load('translations', 'categories', 'media', 'links');
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
