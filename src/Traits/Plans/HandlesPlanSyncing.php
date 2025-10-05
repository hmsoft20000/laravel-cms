<?php

namespace HMsoft\Cms\Traits\Plans;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HandlesPlanSyncing
{
    // A trait can use other traits!
    use \HMsoft\Cms\Traits\Translations\HasTranslations,
        \HMsoft\Cms\Traits\Features\HandlesFeatureSyncing;

    protected function syncPlans(Model $model, ?array $plansData = null): void
    {
        if (!method_exists($model, 'plans')) return;
        if ($plansData === null) return;
        
        $existingIds = $model->plans()->pluck('id')->toArray();
        $incomingIds = Arr::pluck(Arr::whereNotNull($plansData, 'id'), 'id');
        $idsToDelete = array_diff($existingIds, $incomingIds);

        if (!empty($idsToDelete)) {
            $model->plans()->whereIn('id', $idsToDelete)->delete();
        }

        foreach ($plansData ?? [] as $planData) {
            // Create or update the plan itself
            $plan = $model->plans()->updateOrCreate(
                ['id' => $planData['id'] ?? null],
                Arr::except($planData, ['id', 'locales', 'features'])
            );

            // Sync the plan's own translations
            $this->syncTranslations($plan, $planData['locales'] ?? null);

            // Sync the plan's nested features by calling the other trait's method
            $this->syncFeatures($plan, $planData['features'] ?? null);
        }
    }
}
