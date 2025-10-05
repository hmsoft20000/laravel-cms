<?php

namespace HMsoft\Cms\Traits\Features;

use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HandlesFeatureSyncing
{
    use HasTranslations;

    /**
     * Sync features for a given model.
     *
     * @param Model $model The model to sync features for.
     * @param array|null $featuresData The features to sync.
     * @return void
     */
    protected function syncFeatures(Model $model, ?array $featuresData = null): void
    {
        if (!method_exists($model, 'features')) return;
        if ($featuresData === null) return;
        
        $existingIds = $model->features()->pluck('id')->toArray();
        $incomingIds = Arr::pluck(Arr::whereNotNull($featuresData, 'id'), 'id');
        $idsToDelete = array_diff($existingIds, $incomingIds);

        if (!empty($idsToDelete)) {
            $model->features()->whereIn('id', $idsToDelete)->delete();
        }

        foreach ($featuresData ?? [] as $featureData) {
            $feature = $model->features()->updateOrCreate(
                ['id' => $featureData['id'] ?? null],
                Arr::except($featureData, ['id', 'locales'])
            );
            $this->syncTranslations($feature, $featureData['locales'] ?? null);
        }
    }
}
