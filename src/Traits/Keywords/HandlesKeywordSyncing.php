<?php

namespace HMsoft\Cms\Traits\Keywords;

use Illuminate\Database\Eloquent\Model;

trait HandlesKeywordSyncing
{

    /**
     * Sync keywords for a given model.
     *
     * @param Model $model The model to sync keywords for.
     * @param array|null $keywords The keywords to sync.
     * @return void
     */
    protected function syncKeywords(Model $model, ?array $keywords = null): void
    {
        if (!method_exists($model, 'keywords')) return;
        if ($keywords === null) return;
        $model->keywords()->delete();
        $keywordData = array_map(fn($kw) => ['keyword' => $kw], $keywords ?? []);
        if (!empty($keywordData)) {
            $model->keywords()->createMany($keywordData);
        }
    }
}
