<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Http\Resources\Api\PageMetaResource;
use HMsoft\Cms\Models\PageMeta\PageMeta;
use HMsoft\Cms\Repositories\Contracts\PagesMetaRepositoryInterface;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PagesMetaRepository implements PagesMetaRepositoryInterface
{

    use HasTranslations;

    public function __construct(
        private readonly PageMeta $model,
    ) {}

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $createData = collect($data)->except(['locales']);

            $createdModel = $this->model->create($createData->toArray());
            $this->syncTranslations($createdModel, $data['locales'] ?? null);

            $this->refreshCache();
            return $this->show($createdModel);
        });
    }

    public function show(Model $model): Model
    {
        return $model;
    }

    public function update(Model $model, array $data): Model
    {
        $updateData = Arr::except($data, ['locales']);

        return DB::transaction(function () use ($model, $updateData, $data) {
            $model->update($updateData);
            $this->syncTranslations($model, $data['locales'] ?? null);

            $this->refreshCache();
            return $this->show($model);
        });
    }

    /**
     * Update multiple PageMeta records from an array.
     *
     * @param array $pagesData The array of pages data from the request.
     * @return bool
     */
    public function updateMultiple(array $pagesData): bool
    {
        return DB::transaction(function () use ($pagesData) {
            foreach ($pagesData['pages'] as $pageData) {
                $pageMeta = $this->model->find($pageData['id']);
                if ($pageMeta) {
                    $this->update($pageMeta, $pageData);
                }
            }
            return true;
        });
    }

    function refreshCache(): mixed
    {
        $pages_meta = [];
        if (Schema::hasTable('pages_meta')) {
            Cache::forget('pages_meta');
            $pages_meta = Cache::rememberForever('pages_meta', function () {
                return $this->model->with('translations')->get()->map(function ($type) {
                    return (new PageMetaResource($type))->toArray(request());
                })->groupBy('name')->mapWithKeys(function ($value, $key) {
                    return [$key => collect($value)->values()->toArray()[0]];
                });
            });
        }
        return $pages_meta;
    }

    public function delete(Model $model): bool
    {
        $deleted = $model->delete();
        return $deleted;
    }

    public function destroy(Model $model): bool
    {
        $deleted = $model->forceDelete();
        return $deleted;
    }
}
