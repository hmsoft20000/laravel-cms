<?php

namespace HMsoft\Cms\Traits\Downloads;

use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HandlesDownloadSyncing
{
    use HasTranslations;

    /**
     * Sync downloads for a given model.
     *
     * @param Model $model The model to sync downloads for.
     * @param array|null $downloadsData The downloads to sync.
     * @return void
     */
    protected function syncDownloads(Model $model, ?array $downloadsData = null): void
    {
        if (!method_exists($model, 'downloads')) return;
        if ($downloadsData === null) return;
        
        $existingIds = $model->downloads()->pluck('id')->toArray();
        $incomingIds = Arr::pluck(Arr::whereNotNull($downloadsData, 'id'), 'id');
        $idsToDelete = array_diff($existingIds, $incomingIds);

        if (!empty($idsToDelete)) {
            $model->downloads()->whereIn('id', $idsToDelete)->delete();
        }

        foreach ($downloadsData ?? [] as $downloadData) {
            $download = $model->downloads()->updateOrCreate(
                ['id' => $downloadData['id'] ?? null],
                Arr::except($downloadData, ['id', 'locales'])
            );
            $this->syncTranslations($download, $downloadData['locales'] ?? null);
        }
    }
}
