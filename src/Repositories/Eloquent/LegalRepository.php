<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Legal\Legal;
use HMsoft\Cms\Repositories\Contracts\LegalRepositoryInterface;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LegalRepository implements LegalRepositoryInterface
{

    use  HasTranslations;

    public function __construct(private readonly Legal $model) {}

    public function update(Legal $model, array $data): Legal
    {
        return DB::transaction(function () use ($model, $data) {
            $model->update(Arr::except($data, ['locales', 'type']));

            $this->syncTranslations($model, $data['locales'] ?? null);

            return $this->show($model->refresh());
        });
    }

    public function show(Legal $model): Legal
    {
        return $model->load('translations', 'media');
    }
}
