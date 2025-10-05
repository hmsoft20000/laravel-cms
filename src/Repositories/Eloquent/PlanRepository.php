<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Shared\Plan;
use HMsoft\Cms\Models\Shared\PlanFeature;
use HMsoft\Cms\Repositories\Contracts\PlanRepositoryInterface;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PlanRepository implements PlanRepositoryInterface
{
    use HasTranslations;

    public function __construct(private readonly Plan $model) {}


    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $plan = $this->model->create(Arr::except($data, ['locales', 'features']));

            $this->syncTranslations($plan, $data['locales'] ?? null);

            $this->syncFeatures($plan, $data['features'] ?? null);

            return $this->show($plan);
        });
    }

    public function update(Model $plan, array $data): Model
    {
        return DB::transaction(function () use ($plan, $data) {
            /** @var Plan $plan */
            $plan->update(Arr::except($data, ['locales', 'features']));

            $this->syncTranslations($plan, $data['locales'] ?? null);

            $this->syncFeatures($plan, $data['features'] ?? null);

            return $this->show($plan->refresh());
        });
    }

    public function show(Model $model): Model
    {
        return $model->load([
            'translations',
            'features.translations'
        ]);
    }

    public function delete(Model $model): bool
    {
        // ON DELETE CASCADE will handle deleting features and translations.
        return (bool) $model->delete();
    }


    /**
     * Syncs plan features, handling create, update, and delete operations.
     */
    private function syncFeatures(Plan $plan, ?array $featuresData = null): void
    {
        $existingFeatureIds = $plan->features()->pluck('id')->toArray();
        $incomingFeatureIds = Arr::pluck(Arr::whereNotNull($featuresData, 'id'), 'id');
        $idsToDelete = array_diff($existingFeatureIds, $incomingFeatureIds);

        // 1. Delete features that are no longer present
        if (!empty($idsToDelete)) {
            PlanFeature::whereIn('id', $idsToDelete)->delete();
        }

        // 2. Update existing or create new features
        foreach ($featuresData ?? [] as $featureData) {
            $feature = $plan->features()->updateOrCreate(
                ['id' => $featureData['id'] ?? null],
                Arr::except($featureData, ['id', 'locales'])
            );
            $this->syncTranslations($feature, $featureData['locales'] ?? null);
        }
    }
}
